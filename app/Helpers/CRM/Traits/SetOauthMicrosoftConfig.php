<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetOauthMicrosoftConfig
{
  use InstanceCreator;

  public function cashClear()
  {
    Artisan::call('config:clear');
    return $this;
  }

  public function configSet()
  {
    $default = resolve(SettingService::class)->getFormattedSettings('oauth-microsoft');
    Config::set('core.microsoft.redirect_uri', '/admin/microsoft/callback');
    if ($default) {
      Config::set('core.microsoft.client_id', $default['microsoft_client_id']);
      Config::set('core.microsoft.client_secret', $default['microsoft_client_secret']);
    }
  }
}
