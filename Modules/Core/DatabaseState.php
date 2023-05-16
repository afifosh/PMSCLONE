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

namespace Modules\Core;

use Illuminate\Database\Eloquent\Model;

class DatabaseState
{
    protected static array $seeders = [];

    public static function register(string|array $class): void
    {
        static::$seeders = array_merge(static::$seeders, (array) $class);
    }

    public static function flush(): void
    {
        static::$seeders = [];
    }

    public static function seed(): void
    {
        collect(static::$seeders)->map(fn ($class) => new $class)->each(function ($instance) {
            Model::unguarded(function () use ($instance) {
                $instance->__invoke();
            });
        });
    }
}
