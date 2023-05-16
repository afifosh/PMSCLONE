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

use Modules\Core\Date\Carbon;
use Modules\Core\Fields\Field;

class ActivityEndDate extends ActivityDueDate
{
    /**
     * The model attribute that holds the time
     *
     * @var string
     */
    protected $dateField = 'end_date';

    /**
     * The model attribute that holds the date
     *
     * @var string
     */
    protected $timeField = 'end_time';

    /**
     * Field component
     */
    public ?string $component = 'activity-end-date-field';

    /**
     * Initialize new ActivityEndDate instance
     *
     * @param  string  $label
     */
    public function __construct($label)
    {
        parent::__construct($label);

        $this->rules(function ($attribute, $value, $fail) {
            if (empty($value)) {
                return;
            }

            [$attributesDueDate, $attributesEndDate] = $this->attributesForValidation();

            $dueCarbon = $this->carbonInstanceFromStorageAttribute($attributesDueDate);
            $endCarbon = $this->carbonInstanceFromStorageAttribute($attributesEndDate);

            // When all day or both due_date and due_time has time, we will
            // compare the dates directly as it's approriate
            if ((! $attributesEndDate['end_time'] && ! $attributesDueDate['due_time']) ||
                ($attributesEndDate['end_time'] && $attributesDueDate['due_time'])) {
                if ($endCarbon->lessThan($dueCarbon)) {
                    $fail('activities::activity.validation.end_date.less_than_due')->translate();
                }
            } elseif (! $attributesEndDate['end_time'] && $attributesDueDate['due_time']) {
                // Because we cannot compare date with no time with date with time, we will
                // just add the same hour and minute on the end date from the due date to
                // perform the comparision, this will make sure that proper validation and comparision
                // Regular date e.q. 2021-12-14 (in this case it's in user timezone)
                $dueCarbonInUserTimezone = Carbon::inUserTimezone($dueCarbon);

                // Convert it to UTC/App timezone to perform comparision
                $endCarbon = Carbon::asCurrentTimezone($endCarbon->format('Y-m-d H:i:s'))
                    ->hour($dueCarbonInUserTimezone->hour)
                    ->minute($dueCarbonInUserTimezone->minute)
                    ->second(0)
                    ->inAppTimezone();

                if ($endCarbon->lessThan($dueCarbon)) {
                    $fail('activities::activity.validation.end_date.less_than_due')->translate();
                }

                if (! $endCarbon->isSameDay($dueCarbon)) {
                    $fail('activities::activity.validation.end_time.required_when_end_date_is_in_future')->translate();
                }
            }
        });
    }

    /**
     * Create Carbon instance from the given storage attributes
     *
     * @param  array  $attributes
     * @return \Illuminate\Support\Carbon
     */
    protected function carbonInstanceFromStorageAttribute($attributes)
    {
        [$date, $time] = array_values($attributes);

        return Carbon::parse($date.($time ? ' '.$time : ''));
    }

    /**
     * Create attributes that will be used to validate the field value
     *
     * @return array
     */
    protected function attributesForValidation()
    {
        return with($this->resolveRequest(), function ($request) {
            // We will create attributes from the helper method because the time
            // may be provided within the same attribute e.q. end_date => '2021-04-05 22:00:00'
            return [
                $this->createSeparateDateAndTimeAttributes(
                    $request->due_date.($request->due_time ? ' '.$request->due_time : ''),
                    'due_date',
                    'due_time'
                ),

                $this->createSeparateDateAndTimeAttributes(
                    $request->end_date.($request->end_time ? ' '.$request->end_time : '')
                ),
            ];
        });
    }
}
