<?php

namespace App\Listeners;

use App\Events\DeliverySettingUpdated;
use App\Services\Core\Setting\Cache\DeliveryCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheDeliverySetting
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
     * @param  \App\Events\DeliverySettingUpdated  $event
     * @return void
     */
    public function handle(DeliverySettingUpdated $event)
    {
        DeliveryCacheService::handle();
    }
}
