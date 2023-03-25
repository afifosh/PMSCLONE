<?php

namespace App\Listeners;

use App\Events\SecuritySettingUpdated;
use App\Services\Core\Setting\Cache\SettingCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheSetting
{
    private $context;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($context = 'app')
    {
        $this->context = $context;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SecuritySettingUpdated  $event
     * @return void
     */
    public function handle(SecuritySettingUpdated $event)
    {
        SettingCacheService::handle($this->context);
    }
}
