<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetBroadcastingConfig
{
    use InstanceCreator;

    public function cashClear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function configSet()
    {
        $broadcast = resolve(SettingService::class)->getFormattedSettings('broadcast_driver');
        if ($broadcast) {
          Config::set('broadcasting.default', 'pusher');
          Config::set('broadcasting.connections.pusher.key', $broadcast['pusher_app_key']);
          Config::set('broadcasting.connections.pusher.secret', $broadcast['pusher_app_secret']);
          Config::set('broadcasting.connections.pusher.app_id', $broadcast['pusher_app_id']);
          Config::set('broadcasting.connections.pusher.options.cluster', $broadcast['pusher_app_cluster']);
          if($broadcast['broadcast_driver'] == 'websockets'){
            Config::set('broadcasting.connections.pusher.options.scheme', $broadcast['app_scheme']);
            Config::set('broadcasting.connections.pusher.options.host', $broadcast['app_host']);
            Config::set('broadcasting.connections.pusher.options.port', $broadcast['app_port']);
            Config::set('websockets.apps.0.id', $broadcast['pusher_app_id']);
            Config::set('websockets.apps.0.key', $broadcast['pusher_app_key']);
            Config::set('websockets.apps.0.secret', $broadcast['pusher_app_secret']);
          }
        }
    }
}
