<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Contacts\Fields;

use Closure;
use Illuminate\Support\Str;
use Modules\Contacts\Enums\PhoneType;
use Modules\Contacts\Http\Resources\PhoneResource;
use Modules\Contacts\Models\Phone as Model;
use Modules\Contacts\Models\Phone as PhoneModel;
use Modules\Core\CountryCallingCode;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Fields\ChecksForDuplicates;
use Modules\Core\Fields\MorphMany;
use Modules\Core\Table\MorphManyColumn;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Phone extends MorphMany
{
    use ChecksForDuplicates;

    /**
     * Phone types
     */
    public array $types = [];

    /**
     * Default type
     */
    public ?PhoneType $type = null;

    /**
     * Field component
     */
    public ?string $component = 'phone-field';

    /**
     * Display key
     */
    public string $displayKey = 'number';

    /**
     * Calling prefix
     *
     * @var mixed
     */
    public $callingPrefix = null;

    /**
     * Indicates whether the phone should be unique
     *
     * @var \Illuminate\Database\Eloquent\Model|bool
     */
    public $unique = false;

    /**
     * Indicates whether to skip the unique rule validation in import
     *
     * @var bool
     */
    public $uniqueRuleSkipOnImport = true;

    /**
     * Unique rule custom validation message
     *
     * @var string
     */
    public $uniqueRuleMessage;

    protected static string $typeInNumberValueSeparator = '|';

    /**
     * Provide the column used for index
     */
    public function indexColumn(): MorphManyColumn
    {
        return tap(new MorphManyColumn(
            $this->morphManyRelationship,
            $this->displayKey,
            $this->label
        ), function ($column) {
            $column->select('type')->useComponent('table-data-phones-column');
        });
    }

    /**
     * Mark the field as unique
     *
     * @param  string  $model
     * @param  string  $message
     */
    public function unique($model, $message = 'The phone number already exists', $skipOnImport = true): static
    {
        $this->unique = $model;
        $this->uniqueRuleMessage = $message;
        $this->uniqueRuleSkipOnImport = $skipOnImport;

        return $this;
    }

    /**
     * Set the phone types
     */
    public function types(array $types): static
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Set the default phone type
     */
    public function defaultType(PhoneType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the phone field calling prefix
     */
    public function callingPrefix(): string|bool|null
    {
        $prefix = $this->callingPrefix;

        // Default value via callable?
        if (is_callable($prefix)) {
            $prefix = call_user_func($prefix);
        }

        // Provided default value via country ID to take the calling prefix from?
        if (is_int($prefix)) {
            $prefix = CountryCallingCode::fromCountry($prefix);
        }

        // The default calling prefix provided does not start with the + char?
        if (! empty($prefix) && is_string($prefix) && ! Str::startsWith($prefix, '+')) {
            $prefix = '+'.$prefix;
        }

        return $prefix;
    }

    /**
     * Add calling prefix
     */
    public function requireCallingPrefix(bool|int|callable|string|null $default = true): static
    {
        $this->callingPrefix = $default;

        return $this;
    }

    /**
     * Check if the number requires calling prefix
     */
    public function requiresCallingPrefix(): bool
    {
        return ! is_null($this->callingPrefix());
    }

    /**
     * Check whether the given number is unique
     */
    protected function isNumberUnique(array $number): bool
    {
        $where = [
            $this->displayKey => $number[$this->displayKey],
            'phoneable_type' => $this->unique,
        ];

        if (isset($number['id'])) {
            $where[] = ['id', '!=', $number['id']];
        }

        return PhoneModel::where($where)->count() === 0;
    }

    /**
     * If needed, add calling prefix to the given phone
     */
    protected function addCallingPrefixIfNeeded(array &$phone): void
    {
        $prefix = $this->callingPrefix();

        if (! $prefix) {
            return;
        }

        if (empty($phone[$this->displayKey]) || CountryCallingCode::startsWithAny($phone[$this->displayKey])) {
            return;
        }

        if (! Str::startsWith($prefix, $phone[$this->displayKey])) {
            $phone[$this->displayKey] = $prefix.$phone[$this->displayKey];
        }
    }

    /**
     * Boot the field
     *
     * @return void
     */
    public function boot()
    {
        $this->types(collect(PhoneType::names())->mapWithKeys(function ($name) {
            return [$name => __('contacts::fields.phone.types.'.$name)];
        })->all())
            ->defaultType(PhoneType::mobile)
            ->setJsonResource(PhoneResource::class)
            ->rules([
                '*.'.$this->displayKey => [function ($attribute, $value, $fail) {
                    $number = $this->resolveRequest()->input(
                        Str::before($attribute, '.'.$this->displayKey)
                    );

                    if ($number['_delete'] ?? null) {
                        return;
                    }

                    if (! empty($value)) {
                        $this->startsWithPrefixValidationHandler($value, $fail);

                        $this->uniqueValidationHandler($number, $fail);
                    }
                }],
            ])
            ->prepareForValidation(function ($value, $request, $validator, $data) {
                return $this->ensureProperValueFormat($value);
            })->provideSampleValueUsing(function () {
                return [[$this->displayKey => Model::generateRandomPhoneNumber(), 'type' => array_rand($this->types)]];
            })->provideImportValueSampleUsing(function () {
                return implode(',', [Model::generateRandomPhoneNumber(), Model::generateRandomPhoneNumber()]);
            })->saveUsing(function ($request, $requestAttribute, $value, $field) {
                foreach ($value ?? [] as $key => $phone) {
                    if (isset($phone['type']) && ! $phone['type'] instanceof PhoneType) {
                        $value[$key]['type'] = PhoneType::find($phone['type']) ?? $this->type;
                    }

                    // Empty number provided for new phone? Skip in this case, nothing to do
                    // ResourceFulHandlerWithFields will delete the phone when _delete exists
                    if (empty($phone[$this->displayKey]) && ! isset($phone['_delete'])) {
                        unset($value[$key]);
                    }
                }

                return [$field->attribute => $value];
            });
    }

    /**
     * Mark the field as not unique
     */
    public function notUnique(): static
    {
        // Perform reset
        $this->unique = false;
        $this->uniqueRuleSkipOnImport = true;
        $this->uniqueRuleMessage = null;

        return $this;
    }

    /**
     * Check whether the field is unique
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Unique validation handler
     */
    protected function uniqueValidationHandler(array $number, Closure $fail): void
    {
        if ($this->shouldSkipUniqueValidation()) {
            return;
        }

        if (! $this->isNumberUnique($number)) {
            $fail($this->uniqueRuleMessage);
        }
    }

    /**
     * Validation if the number starts with the specified field prefix
     */
    protected function startsWithPrefixValidationHandler(string $number, Closure $fail): void
    {
        if ($this->requiresCallingPrefix() && ! CountryCallingCode::startsWithAny($number)) {
            $fail('validation.calling_prefix')->translate(['attribute' => $this->label]);
        }
    }

    /**
     * Check whether the unique validation should be skipped
     */
    protected function shouldSkipUniqueValidation(): bool
    {
        return ! $this->unique || (Innoclapps::isImportInProgress() && $this->uniqueRuleSkipOnImport);
    }

    /**
     * Ensure that the provided phone value is in proper format
     */
    protected function ensureProperValueFormat(string|array|null $value): ?array
    {
        // Allow providing the phone number as string, used on import, API, Zapier etc...
        // Possible values: "+55282855292929" "+55282855292929|work" "+55282855292929,+123123558922|other"
        // Note that when the phone type is not provided, will use the default type
        if (! is_array($value) && ! empty($value)) {
            $value = array_map(fn ($number) => [$this->displayKey => trim($number)], explode(',', $value));
        }

        if (! is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $phone) {
            // Allow providing the phone only e.q. ['+55282855292929', '+123123558922', '+123123558922|work']
            $value[$key] = ! is_array($phone) ? [$this->displayKey => $phone] : $value[$key];

            // Next, we will check if the calling prefix should be added automatically
            $this->addCallingPrefixIfNeeded($value[$key]);

            // Allow providing the type via the phone, separated by pipe e.q. +55282855292929|work
            $this->extractTypeFromNumber(static::$typeInNumberValueSeparator, $value[$key]);

            $value[$key]['_track_by'] = [$this->displayKey => $value[$key][$this->displayKey]];

            // Handle deletition when phone exists but value is provided empty
            if (empty($value[$key][$this->displayKey]) && isset($value[$key]['id'])) {
                $value[$key]['_delete'] = true;
            }
        }

        return $value;
    }

    /**
     * Allow user to provide type in the number via the provided separator
     */
    protected function extractTypeFromNumber(string $separator, array &$phone): void
    {
        $number = $phone[$this->displayKey];

        if ($number && str_contains($number, $separator)) {
            if ($type = PhoneType::find(strtolower(Str::afterLast($number, $separator)))) {
                $phone['type'] = $type;
                $phone[$this->displayKey] = Str::beforeLast($number, $separator);
            }
        }
    }

    /**
     * Generate random phone digits
     */
    protected function randomPhoneDigits(int $digits = 3): int
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    /**
     * Indicates that the relation will be counted
     */
    public function count(): static
    {
        throw new \Exception('The '.class_basename(__CLASS__).' field does not support counting.');
    }

    /**
     * Laravel Excel changes the data type of the phone value to integer even when the + sign is included
     * We need string, so the + sign remains in the value and we can properly check whether the phone
     * starts with a valid country calling prefix
     */
    public function importValueDataType(): string
    {
        return DataType::TYPE_STRING;
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'types' => $this->types,
            'type' => $this->type?->name,
            'callingPrefix' => value(function () {
                $prefix = $this->callingPrefix();

                return $prefix === true ? null : $prefix;
            }),
        ]);
    }
}
