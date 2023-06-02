<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetOauthGoogleConfig
{
  use InstanceCreator;

  public function cashClear()
  {
    Artisan::call('config:clear');
    return $this;
  }

  public function configSet()
  {
    $default = resolve(SettingService::class)->getFormattedSettings('oauth-google');
    Config::set('core.google.redirect_uri', '/admin/google/callback');
    if ($default) {
      Config::set('core.google.client_id', $default['google_client_id']);
      Config::set('core.google.client_secret', $default['google_client_secret']);
    }
  }
}
