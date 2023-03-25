<?php

namespace App\Services\Core\Setting\Broadcast\ProviderConfiguration;

use App\Services\Core\Contracts\BootConfiguration;

class Pusher implements BootConfiguration
{
    public function load($configurations)
    {
        config()->set('services.broadcast_custom_driver', $configurations['broadcast_driver']);

        config()->set('broadcasting.default', $configurations['broadcast_driver']);
        config()->set('broadcasting.connections.pusher.key', $configurations['pusher_app_key']);
        config()->set('broadcasting.connections.pusher.secret', $configurations['pusher_app_secret']);
        config()->set('broadcasting.connections.pusher.app_id', $configurations['pusher_app_id']);
        config()->set('broadcasting.connections.pusher.options.cluster', $configurations['pusher_app_cluster']);
    }
}
