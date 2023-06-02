<?php


namespace App\Helpers\CRM\Traits;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetOnlyOfficeConfig
{
    use InstanceCreator;

    public function cashClear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function configSet()
    {
        $default = resolve(SettingService::class)->getFormattedSettings('onlyoffice');

        if ($default) {
                Config::set('onlyoffice.secret', $default['secret']);
                Config::set('onlyoffice.doc_server_url', $default['doc_server_url']);
                Config::set('onlyoffice.doc_server_api_url', $default['doc_server_api_url']);
                Config::set('onlyoffice.allowed_file_size', $default['allowed_file_size']);
                Config::set('onlyoffice.supported_files', $default['supported_files']);
        }

    }
}
