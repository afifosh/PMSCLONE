<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetAppSecurityConfig
{
    use InstanceCreator;

    public function cashClear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function configSet()
    {
        $default = resolve(SettingService::class)->getFormattedSettings('security');

        if ($default) {
                Config::set('auth.enable_timeout', $default['enable_timeout']);
                Config::set('auth.timeout_warning_seconds', $default['timeout_warning_seconds']);
                Config::set('auth.timeout_after_seconds', $default['timeout_after_seconds']);
        }

    }
}
