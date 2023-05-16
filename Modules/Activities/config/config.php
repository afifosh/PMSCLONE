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

return [
    'name' => 'Activities',

    /*
    |--------------------------------------------------------------------------
    | Application defaults config
    |--------------------------------------------------------------------------
    | Here you can specify defaults configurations that the application
    | uses when configuring specific option e.q. creating a follow up task
    | automatically uses the configured hour and minutes.
    |
    */
    'defaults' => [
        'hour' => env('PREFERRED_DEFAULT_HOUR', 8),
        'minutes' => env('PREFERRED_DEFAULT_MINUTES', 0),
        'reminder_minutes' => env('PREFERRED_DEFAULT_REMINDER_MINUTES', 30),
    ],
];
