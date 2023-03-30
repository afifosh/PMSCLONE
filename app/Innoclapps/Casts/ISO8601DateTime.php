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

class ISO8601DateTime implements CastsAttributes
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

        return Carbon::createFromFormat(
            $model->getDateFormat(),
            $value
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

        if (Carbon::isISO8601($value)) {
            $value = Carbon::parse($value)->inAppTimezone();
        }

        return $model->fromDateTime($value);
    }
}
