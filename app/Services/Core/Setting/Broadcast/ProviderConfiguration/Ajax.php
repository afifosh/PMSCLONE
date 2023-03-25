<?php

namespace App\Services\Core\Setting\Broadcast\ProviderConfiguration;

use App\Services\Core\Contracts\BootConfiguration;

class Pusher implements BootConfiguration
{
    public function load($configurations)
    {
        config()->set('services.broadcast_custom_driver', 'ajax');
    }
}
