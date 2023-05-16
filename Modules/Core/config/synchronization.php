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
    /*
    |--------------------------------------------------------------------------
    | Synchronization interval definition
    |--------------------------------------------------------------------------
    |
    | For periodic synchronization like Google, the events by default
    | are synchronized every 3 minutes, the interval can be defined below in cron style.
    */
    'interval' => env('SYNC_INTERVAL', '*/3 * * * *'),
];
