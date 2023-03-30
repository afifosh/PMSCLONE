<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Fields;

use ReflectionClass;
use Illuminate\Support\Arr;
use App\Innoclapps\Facades\Fields;
use Illuminate\Support\Collection;
use App\Innoclapps\SubClassDiscovery;
use App\Innoclapps\Contracts\Fields\Customfieldable;
use App\Innoclapps\Contracts\Fields\CustomfieldUniqueable;
use App\Innoclapps\Contracts\Repositories\CustomFieldRepository;

class Manager
{
    /**
     * Hold all groups and fields
     */
    protected array $fields = [];

    /**
     * Loaded fields cache
     */
    protected static array $loaded = [];

    /**
     * The files that are custom field able
     */
    protected ?array $customFieldable = null;

    /**
     * Parsed custom fields cache
     */
    protected ?Collection $customFields = null;

    /**
     * Register fields with group
     *
     * @param string $group
     * @param mixed $provider
     *
     * @return static
     */
    public function group($group, $provider)
    {
        static::flushCache();

        if (! isset($this->fields[$group])) {
            $this->fields[$group] = [];
        }

        $this->fields[$group][] = $provider;

        return $this;
    }

    /**
     * Add fields to the given group
     *
     * @param string $group
     * @param mixed $provider
     *
     * @return static
     */
    public function add($group, $provider)
    {
        return $this->group($group, $provider);
    }

    /**
     * Replace the group fields with the given fields
     *
     * @param string $group
     * @param mixed $provider
     *
     * @return static
     */
    public function replace($group, $provider)
    {
        $this->fields[$group] = [];

        return $this->group($group, $provider);
    }

    /**
     * Resolves fields for the given group and view
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolve(string $group, string $view)
    {
        return $this->{'resolve' . ucfirst($view) . 'Fields'}($group);
    }

    /**
     * Resolves fields for the given group and view for display
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveForDisplay(string $group, string $view)
    {
        return $this->{'resolve' . ucfirst($view) . 'FieldsForDisplay'}($group);
    }

    /**
     * Resolve the create fields for display
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveCreateFieldsForDisplay(string $group)
    {
        return $this->resolveCreateFields($group)
            ->reject(fn ($field) => $field->showOnCreation === false)
            ->values();
    }

    /**
     * Resolve the update fields for display
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveUpdateFieldsForDisplay(string $group)
    {
        return $this->resolveUpdateFields($group)
            ->reject(fn ($field) => $field->showOnUpdate === false)
            ->values();
    }

    /**
     * Resolve the detail fields for display
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveDetailFieldsForDisplay(string $group)
    {
        return $this->resolveDetailFields($group)
            ->reject(fn ($field) => $field->showOnDetail === false)
            ->values();
    }

    /**
     * Resolve the create fields for the given group
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveCreateFields(string $group)
    {
        return $this->resolveAndAuthorize($group, Fields::CREATE_VIEW)
            ->filter->isApplicableForCreation()->values();
    }

    /**
     * Resolve the update fields for the given group
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveUpdateFields(string $group)
    {
        return $this->resolveAndAuthorize($group, Fields::UPDATE_VIEW)
            ->filter->isApplicableForUpdate()->values();
    }

    /**
     * Resolve the detail fields for the given group
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveDetailFields(string $group)
    {
        return $this->resolveAndAuthorize($group, Fields::DETAIL_VIEW)
            ->filter->isApplicableForDetail()->values();
    }

    /**
     * Resolve and authorize the fields for the given group
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveAndAuthorize(string $group, ?string $view = null)
    {
        return $this->inGroup($group, $view)->filter->authorizedToSee();
    }

    /**
     * Resolve the fields intended for settings
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveForSettings(string $group, string $view)
    {
        return $this->resolveAndAuthorize($group, $view)->reject(function ($field) use ($view) {
            return is_bool($field->excludeFromSettings) ? $field->excludeFromSettings : $field->excludeFromSettings === $view;
        })->values();
    }

    /**
     * Get all fields in specific group
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function inGroup(string $group, ?string $view = null)
    {
        if (isset(static::$loaded[$cacheKey = (string) $group . $view])) {
            return static::$loaded[$cacheKey];
        }

        $callback = function ($field, $index) use ($group, $view) {
            /**
             * Apply any custom attributes added by the user via settings
             */
            $field = $this->applyCustomizedAttributes($field, $group, $view);

            /**
             * Add field order if there is no customized order
             * This helps to sort them properly by default the way they are defined
             */
            $field->order ??= $index + 1 ;

            return $field;
        };

        static::$loaded[$cacheKey] = $this->load($group)->map($callback)->sortBy('order')->values();

        return static::$loaded[$cacheKey];
    }

    /**
     * Save the customized fields
     */
    public function customize(mixed $data, string $group, string $view) : void
    {
        settings([$this->customizedKey($group, $view) => json_encode($data)]);

        static::flushCache();
    }

    /**
     * Get the customized fields
     */
    public function customized(string $group, string $view) : mixed
    {
        $customized = settings()->get($this->customizedKey($group, $view), '[]');

        return json_decode($customized);
    }

    /**
     * Purge the customized fields cache
     */
    public static function flushCache() : void
    {
        static::$loaded = [];
    }

    /**
     * Get the available fields that can be used as custom fields
     */
    public function customFieldable() : Collection
    {
        if ($this->customFields) {
            return $this->customFields;
        }

        if (! $this->customFieldable) {
            $this->customFieldable = (new SubClassDiscovery(Customfieldable::class, __DIR__))->find();
        }

        return $this->customFields = collect($this->customFieldable)->mapWithKeys(function ($className) {
            $field = (new ReflectionClass($className))->newInstanceWithoutConstructor();
            $type = class_basename($className);

            return [$type => [
                'type'            => $type,
                'className'       => $className,
                'uniqueable'      => $field instanceof CustomfieldUniqueable,
                'optionable'      => $field->isOptionable(),
                'multioptionable' => $field->isMultiOptionable(),
            ]];
        });
    }

    /**
     * Get the multi optionable custom fields types
     */
    public function getOptionableCustomFieldsTypes() : array
    {
        return $this->customFieldable()->where('optionable', true)->keys()->all();
    }

    /**
     * Get non optionable custom fields types
     */
    public function getNonOptionableCustomFieldsTypes() : array
    {
        return array_diff($this->customFieldsTypes(), $this->getOptionableCustomFieldsTypes());
    }

    /**
     * Get the available custom fields types
     */
    public function customFieldsTypes() : array
    {
        return $this->customFieldable()->keys()->all();
    }

    /**
     * Get the custom fields that can be marked as unique
     */
    public function getUniqueableCustomFieldsTypes() : array
    {
        return $this->customFieldable()->where('uniqueable', true)->keys()->all();
    }

    /**
     * Get the defined custom fields for the given resource
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCustomFieldsForResource(string $resourceName)
    {
        $repository = resolve(CustomFieldRepository::class);

        return $repository->forResource($resourceName)->map(
            fn ($field) => CustomFieldFactory::createInstance($field)
        );
    }

    /**
     * Loaded the provided group fields
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    protected function load(string $group)
    {
        $fields = new FieldsCollection();

        foreach ($this->fields[$group] ?? [] as $provider) {
            if ($provider instanceof Field) {
                $provider = [$provider];
            }

            if (is_array($provider)) {
                $fields = $fields->merge($provider);
            } elseif (is_callable($provider)) { // callable, closure, __invoke
                $fields = $fields->merge(call_user_func($provider));
            }
        }

        return $fields->merge($this->getCustomFieldsForResource($group));
    }

    /**
     * Create customized key for settings
     */
    protected function customizedKey(string $group, string $view) : string
    {
        return "fields-{$group}-{$view}";
    }

    /**
     * Get the allowed customize able attributes
     */
    public function allowedCustomizableAttributes() : array
    {
        return ['order', 'showOnCreation', 'showOnUpdate', 'showOnDetail', 'collapsed', 'isRequired'];
    }

    /**
     * Get the allowed customize able attributes
     */
    public function allowedCustomizableAttributesForPrimary() : array
    {
        return ['order'];
    }

    /**
     * Apply any customized options by user
     */
    protected function applyCustomizedAttributes(Field $field, string $group, ?string $view) : Field
    {
        // Allowed customizable attributes for all fields
        $allowedAttributes = $this->allowedCustomizableAttributes();

        // Protected the primary fields visibility and collapse options when direct API request
        // e.q. the field visibility is set to false when it must be visible because the field is marked as primary field
        $allowedAttributesForPrimary = $this->allowedCustomizableAttributesForPrimary();

        if ($view && $customizedData = $this->customized($group, $view)) {
            if (isset($customizedData->{$field->attribute})) {
                $attributes = Arr::only(
                    get_object_vars($customizedData->{$field->attribute}),
                    $field->isPrimary() ? $allowedAttributesForPrimary : $allowedAttributes
                );

                foreach ($attributes as $attribute => $value) {
                    if ($attribute === 'isRequired' && $value == true) {
                        $field->rules(['sometimes', 'required']);
                    } else {
                        $field->{$attribute} = $value;
                    }
                }
            }
        }

        return $field;
    }
}
