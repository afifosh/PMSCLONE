<?php

namespace App\Listeners;

use App\Events\DeliverySettingUpdated;
use App\Traits\DeliverySetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheDeliverySetting
{
    use DeliverySetting;

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
        $this->updateCache();
    }
}
