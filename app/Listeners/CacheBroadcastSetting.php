<?php

namespace App\Listeners;

use App\Events\BroadcastSettingUpdated;
use App\Services\Core\Setting\Cache\BroadcastCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheBroadcastSetting
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\BroadcastSettingUpdated  $event
     * @return void
     */
    public function handle(BroadcastSettingUpdated $event)
    {
        BroadcastCacheService::handle();
    }
}
