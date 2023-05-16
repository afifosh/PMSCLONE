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

namespace Modules\Activities\Fields;

use Modules\Activities\Models\Activity;
use Modules\Core\Date\Carbon;
use Modules\Core\Facades\Format;
use Modules\Core\Fields\Date;
use Modules\Core\Placeholders\DatePlaceholder;
use Modules\Core\Placeholders\DateTimePlaceholder;
use Modules\Core\Resource\Http\ResourceRequest;

class ActivityDueDate extends Date
{
    /**
     * The model attribute that hold the time
     *
     * @var string
     */
    protected $timeField = 'due_time';

    /**
     * The model attribute that holds the date
     *
     * @var string
     */
    protected $dateField = 'due_date';

    /**
     * Initialize new ActivityDueDate instance class
     */
    public function __construct($label)
    {
        parent::__construct($this->dateField, $label);

        $this->tapIndexColumn(function ($column) {
            return $column->width('180px')
                ->queryAs(Activity::dateTimeExpression($this->dateField, $this->timeField, $this->dateField))
                ->displayAs(function ($model) {
                    return $model->{$this->timeField} ? Format::dateTime(
                        $model->{$this->dateField}
                    ) : Format::date($model->{$this->dateField});
                });
        })->provideSampleValueUsing(fn () => date('Y-m-d').' 08:00:00')
            ->prepareForValidation(
                fn ($value, $request, $validator) => $this->mergeAttributesBeforeValidation($value, $request)
            );
    }

    /**
     * Field component
     */
    public ?string $component = 'activity-due-date-field';

    /**
     * Resolve the field value for JSON Resource
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveForJsonResource($model)
    {
        return [
            $this->attribute => [
                'date' => Carbon::parse($this->resolve($model))->format('Y-m-d'),
                'time' => $this->getTimeValue($model),
            ],
        ];
    }

    /**
     * Resolve the field value for export
     *
     * @param  \Modules\Core\Models\Model  $model
     * @return string|null
     */
    public function resolveForExport($model)
    {
        $time = $this->getTimeValue($model);

        $carbonInstance = $this->dateTimeToCarbon($model->{$this->dateField}, $time);

        return $carbonInstance->format('Y-m-d'.($time ? ' H:i:s' : ''));
    }

    /**
     * Resolve the displayable field value
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string|null
     */
    public function resolveForDisplay($model)
    {
        return Format::separateDateAndTime(
            $model->{$this->dateField},
            $model->{$this->timeField},
            $model->user
        );
    }

    /**
     * Resolve the field value for import
     *
     * @param  string|null  $value
     * @param  array  $row
     * @param  array  $original
     * @return array
     */
    public function resolveForImport($value, $row, $original)
    {
        $value = parent::resolveForImport(
            $value,
            $row,
            $original
        )[$this->attribute];

        return $this->createSeparateDateAndTimeAttributes($value);
    }

    /**
     * Create the field storage data for the given request
     *
     * @param  string  $requestAttribute
     * @return array
     */
    public function getDataForStorage(ResourceRequest $request, $requestAttribute)
    {
        return $this->createSeparateDateAndTimeAttributes(
            $this->attributeFromRequest($request, $requestAttribute)
        );
    }

    /**
     * Get the mailable template placeholder
     *
     * @param  \Modules\Core\Models\Model|null  $model
     * @return \Modules\Core\Placeholders\DatePlaceholder|\Modules\Core\Placeholders\DateTimePlaceholder
     */
    public function mailableTemplatePlaceholder($model)
    {
        $placeholderClass = $model?->{$this->timeField} ?
            DateTimePlaceholder::class :
            DatePlaceholder::class;

        return $placeholderClass::make()
            ->formatUsing(function () use ($model) {
                return $this->resolveForDisplay($model);
            })
            ->tag($this->attribute)
            ->description($this->label);
    }

    /**
     * Create separate and and time attributes from the given value
     *
     * @param  string|null  $value
     * @param  string|null  $dateAttribute
     * @param  string|null  $timeAttribute
     * @return array
     */
    protected function createSeparateDateAndTimeAttributes($value, $dateAttribute = null, $timeAttribute = null)
    {
        $dateAttribute = ($dateAttribute ?: $this->dateField);
        $timeAttribute = ($timeAttribute ?: $this->timeField);

        [$date, $time] = [$value, null];

        if (Carbon::isISO8601($value)) {
            $value = Carbon::parse($value)->inAppTimezone();
        }

        if (! is_null($value) && str_contains($value, ' ')) {
            [$date, $time] = explode(' ', $value);
        }

        // Overrides if the date is provided in full e.q. 2021-12-14 12:00:00
        // and the user provide time field e.q. 14:00:00 the 14:00:00 will be used
        if (! $time && $this->resolveRequest()->has($timeAttribute)) {
            $time = $this->resolveRequest()->{$timeAttribute};
        }

        return [
            $dateAttribute => $date,
            $timeAttribute => $time,
        ];
    }

    /**
     * Merge the attributes before validating
     *
     * @param  mixed  $value
     * @param  \Modules\Core\Resource\Http\ResourceRequest  $request
     * @return string|null
     */
    protected function mergeAttributesBeforeValidation($value, $request)
    {
        $attributes = $this->createSeparateDateAndTimeAttributes($value);

        // When provided the field as full date and time e.q. 2021-12-14 12:00:00 the time field
        // will be missing in the request, we need to merge it with the other fields
        if ($request->missing($this->timeField)) {
            $request->merge([$this->timeField => $attributes[$this->timeField]]);
        }

        // Return the actul date value from the parsed attributes
        return $attributes[$this->dateField];
    }

    /**
     * Get the time value from the model
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string|null
     */
    protected function getTimeValue($model)
    {
        if (! $model->{$this->timeField}) {
            return null;
        }

        return $this->dateTimeToCarbon(
            $this->resolve($model),
            $model->{$this->timeField}
        )->format('H:i');
    }

    /**
     * Create Carbon UTC instance from the given date and time
     *
     * @param  string  $date
     * @param  string|null  $time
     * @return \Carbon\Carbon
     */
    protected function dateTimeToCarbon($date, $time)
    {
        return Carbon::parse(
            Carbon::parse($date)->format('Y-m-d').($time ? ' '.$time : '')
        );
    }
}
