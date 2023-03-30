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

namespace App\Innoclapps\Casts;

use App\Innoclapps\Date\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ISO8601Date implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param string|null $value
     * @param array $attributes
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) {
            return null;
        }

        // The Spatie activity log library is passing the value with 00:00:00
        // We don't need the leading zero's as there will be trailing data error
        return Carbon::instance(
            Carbon::createFromFormat(
                'Y-m-d',
                explode(' ', $value)[0]
            )->startOfDay()
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (empty($value)) {
            return null;
        }

        // We will check if the provided value is ISO8601 date, if yes, we will parse it with Carbon
        // without setting or modifying the timezone like in ISO8601DateTime cast.
        // For example, Zapier for dates only provides dates in this format: 2018-03-02T00:00:00+01:00
        // This is 2018-03-02 date, and we need to keep it like this without timezone modification
        // if we do Carbon::parse($date)->tz('UTC') the date will be 2018-03-01 as in UTC this date
        // is equal to 2018-03-01 23:00:00
        if (Carbon::isISO8601($value)) {
            $value = Carbon::parse($value);
        }

        return $model->fromDateTime($value);
    }
}
