<?php

namespace App\Listeners;

use App\Events\SecuritySettingUpdated;
use App\Services\Core\Setting\Cache\SettingCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheSetting
{
    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        SettingCacheService::handle($event->context);
    }
}
