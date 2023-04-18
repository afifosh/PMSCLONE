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

namespace App\Innoclapps\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the model uuid generator trait
     *
     * @return void
     */
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (! $model->{static::getUuidColumnName()}) {
                $model->{static::getUuidColumnName()} = static::generateUuid();
            }
        });
    }

    /**
     * Generate model uuid
     *
     * @return string
     */
    public static function generateUuid() : string
    {
        $uuid = null;
        do {
            if (! static::where(static::getUuidColumnName(), $possible = Str::uuid())->first()) {
                $uuid = $possible;
            }
        } while (! $uuid);

        return $uuid;
    }

    /**
     * Get the model uuid column name
     *
     * @return string
     */
    protected static function getUuidColumnName() : string
    {
        return 'uuid';
    }
}
