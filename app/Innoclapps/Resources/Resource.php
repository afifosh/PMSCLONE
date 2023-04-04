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

namespace App\Innoclapps\Resources;

use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Innoclapps\Facades\Menu;
use App\Innoclapps\Facades\Cards;
use App\Innoclapps\Facades\Fields;
use App\Innoclapps\ResolvesActions;
use App\Innoclapps\ResolvesFilters;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Settings\SettingsMenu;
use App\Innoclapps\Resources\Import\Import;
use App\Innoclapps\Fields\CustomFieldFactory;
use App\Innoclapps\Resources\Import\ImportSample;
use App\Innoclapps\Contracts\Resources\Importable;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Innoclapps\Contracts\Resources\Resourceful;
use App\Innoclapps\Contracts\Resources\AcceptsUniqueCustomFields;
use App\Innoclapps\Contracts\Resources\ResourcefulRequestHandler;

abstract class Resource implements JsonSerializable
{
    use ResolvesActions,
        ResolvesFilters,
        QueriesResources,
        ResolvesTables;

    /**
     * The column the records should be default ordered by when retrieving
     */
    public static string $orderBy = 'id';

    /**
     * The direction the records should be default ordered by when retrieving
     */
    public static string $orderByDir = 'asc';

    /**
     * Indicates whether the resource is globally searchable
     */
    public static bool $globallySearchable = false;

    /**
     * Indicates whether the resource fields are customizeable
     */
    public static bool $fieldsCustomizable = false;

    /**
     * Indicates whether the resource has Zapier hooks
     */
    public static bool $hasZapierHooks = false;

    /**
     * The model the resource is related to
     */
    public static string $model;

    /**
     * The underlying model resource instance
     *
     * @var \App\Innoclapps\Models\Model|null
     */
    public $resource;

    /**
     * Record finder instance
     */
    protected ?RecordFinder $finder = null;

    /**
     * Initialize new Resource class
     */
    public function __construct()
    {
        $this->register();
    }

    /**
     * Get the underlying resource repository
     *
     * @return \App\Innoclapps\Repository\AppRepository
     */
    abstract public static function repository();

    /**
     * Get the resource underlying model class name
     *
     * @return string
     */
    public static function model()
    {
        return static::$model;
    }

    /**
     * Set the resource model instance
     *
     * @param \App\Innoclapps\Models\Model|null $resource
     */
    public function setModel($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set the resource available cards
     */
    public function cards() : array
    {
        return [];
    }

    /**
    *  Get the filters intended for the resource
    *
    * @return \Illuminate\Support\Collection
    */
    public function filtersForResource(ResourceRequest $request)
    {
        return $this->resolveFilters($request)->merge(
            (new CustomFieldFactory($this->name()))->createFieldsForFilters()
        );
    }

    /**
     * Get the actions intended for the resource
     *
     * @return \Illuminate\Support\Collection
     */
    public function actionsForResource(ResourceRequest $request)
    {
        return $this->resolveActions($request);
    }

    /**
      * Get the json resource that should be used for json response
      */
    public function jsonResource() : ?string
    {
        return null;
    }

    /**
     * Create JSON Resource
     *
     * @return mixed
     */
    public function createJsonResource(mixed $data, bool $resolve = false, ?ResourceRequest $request = null)
    {
        $collection = is_countable($data);

        if ($collection) {
            $resource = $this->jsonResource()::collection($data);
        } else {
            $jsonResource = $this->jsonResource();
            $resource     = new $jsonResource($data);
        }

        if ($resolve) {
            $request = $request ?: app(ResourceRequest::class)->setResource($this->name());

            if (! $collection) {
                $request->setResourceId($data->getKey());
            }

            return $resource->resolve($request);
        }

        return $resource;
    }

    /**
     * Get the fields that should be included in JSON resource
     *
     * @param \App\Innoclapps\Resources\Http\Request $request
     * @param \App\Innoclapps\Models\Model $model
     *
     * Indicates whether the current user can see the model in the JSON resource
     * @param boolean $canSeeResource
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function getFieldsForJsonResource($request, $model, $canSeeResource = true)
    {
        return $this->resolveFields()->reject(function ($field) use ($request) {
            return $field->excludeFromZapierResponse && $request->isZapier();
        })->filter(function ($field) use ($canSeeResource) {
            if (! $canSeeResource) {
                return $field->alwaysInJsonResource === true;
            }

            return $canSeeResource;
        })->reject(function ($field) use ($model) {
            return is_null($field->resolveForJsonResource($model));
        })->values();
    }

    /**
     * Set the available resource fields
     */
    public function fields(Request $request) : array
    {
        return [];
    }

    /**
     * Get the resource defined fields
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public static function getFields()
    {
        return Fields::inGroup(static::name());
    }

    /**
     * Resolve the create fields for resource
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveCreateFields()
    {
        return Fields::resolveCreateFields(static::name());
    }

    /**
     * Resolve the update fields for the resource
     *
     * @return \App\Innoclapps\Fields\Collection
     */
    public function resolveUpdateFields()
    {
        return Fields::resolveUpdateFields(static::name());
    }

    /**
     * Resolve the resource fields for display
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    public function resolveFields()
    {
        return static::getFields()->filter->authorizedToSee()->values();
    }

    /**
     * Set the resource rules available for create and update
     */
    public function rules(Request $request) : array
    {
        return [];
    }

    /**
     * Set the resource rules available only for create
     */
    public function createRules(Request $request) : array
    {
        return [];
    }

    /**
     * Set the resource rules available only for update
     */
    public function updateRules(Request $request) : array
    {
        return [];
    }

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria() : ?string
    {
        return null;
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName() : ?string
    {
        return null;
    }

    /**
     * Set the menu items for the resource
     */
    public function menu() : array
    {
        return [];
    }

    /**
     * Get the settings menu items for the resource
     */
    public function settingsMenu() : array
    {
        return [];
    }

    /**
     * Register permissions for the resource
     */
    public function registerPermissions() : void
    {
    }

    /**
     * Get the custom validation messages for the resource
     * Useful for resources without fields.
     */
    public function validationMessages() : array
    {
        return [];
    }

    /**
     * Determine whether the resource has associations
     */
    public function isAssociateable() : bool
    {
        return ! is_null($this->associateableName());
    }

    /**
     * Get the resource available associative resources
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableAssociations()
    {
        return Innoclapps::registeredResources()
            ->reject(fn ($resource) => is_null($resource->associateableName()))
            ->filter(fn ($resource) => app(static::$model)->isRelation($resource->associateableName()))
            ->values();
    }

    /**
     * Check whether the given resource can be associated to the current resource
     */
    public function canBeAssociated(string $resourceName) : bool
    {
        return (bool) $this->availableAssociations()->first(
            fn ($resource) => $resource->name() == $resourceName
        );
    }

    /**
     * Get the resourceful CRUD handler class
     *
     * @param \App\Innoclapps\Repository\AppRepository|null $repository
     */
    public function resourcefulHandler(ResourceRequest $request, $repository = null) : ResourcefulRequestHandler|ResourcefulHandlerWithFields
    {
        $repository ??= static::repository();

        return count($this->fields($request)) > 0 ?
            new ResourcefulHandlerWithFields($request, $repository) :
            new ResourcefulHandler($request, $repository);
    }

    /**
     * Determine if this resource is searchable
     */
    public static function searchable() : bool
    {
        return ! empty(static::repository()->getFieldsSearchable());
    }

    /**
     * Get the displayable label of the resource
     */
    public static function label() : string
    {
        return Str::plural(Str::title(Str::snake(class_basename(get_called_class()), ' ')));
    }

    /**
     * Get the displayable singular label of the resource
     */
    public static function singularLabel() : string
    {
        return Str::singular(static::label());
    }

    /**
     * Get the internal name of the resource
     */
    public static function name() : string
    {
        return Str::plural(Str::kebab(class_basename(get_called_class())));
    }

    /**
     * Get the internal singular name of the resource
     */
    public static function singularName() : string
    {
        return Str::singular(static::name());
    }

    /**
     * Get the resource importable class
     */
    public function importable() : Import
    {
        return new Import($this);
    }

    /**
     * Get the resource import sample class
     */
    public function importSample() : ImportSample
    {
        return new ImportSample($this);
    }

    /**
     * Get the resource export class
     *
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     */
    public function exportable($repository) : Export
    {
        return new Export($this, $repository);
    }

    /**
     * Register the resource available menu items
     */
    protected function registerMenuItems() : void
    {
        foreach ($this->menu() as $item) {
            if (! $item->singularName) {
                $item->singularName($this->singularLabel());
            }

            Menu::register($item);
        }
    }

    /**
     * Register the resource settings menu items
     */
    protected function registerSettingsMenuItems() : void
    {
        foreach ($this->settingsMenu() as $key => $item) {
            SettingsMenu::register($item, is_int($key) ? $this->name() : $key);
        }
    }


    /**
     * Register the resource available CRUD fields
     */
    protected function registerFields() : void
    {
        Fields::group($this->name(), fn () => $this->fields(request()));
    }

    /**
     * Register common permissions for the resource
     */
    protected function registerCommonPermissions() : void
    {
        if ($callable = config('innoclapps.resources.permissions.common')) {
            (new $callable)($this);
        }
    }

    /**
     * Get the record finder instance
     */
    public function finder() : RecordFinder
    {
        if ($this->finder) {
            return $this->finder;
        }

        return $this->finder = new RecordFinder(
            $this->repository()
        );
    }

    /**
     * Register the resource information
     */
    protected function register() : void
    {
        $this->registerPermissions();

        if ($this instanceof Resourceful) {
            $this->registerFields();
        }

        Innoclapps::booting(function () {
            $this->registerMenuItems();
            $this->registerSettingsMenuItems();
        });
    }

    /**
     * Serialize the resource
     */
    public function jsonSerialize() : array
    {
        return [
            'name'                      => $this->name(),
            'label'                     => $this->label(),
            'singularLabel'             => $this->singularLabel(),
            'fieldsCustomizable'        => static::$fieldsCustomizable,
            'acceptsUniqueCustomFields' => $this instanceof AcceptsUniqueCustomFields,
        ];
    }
}
