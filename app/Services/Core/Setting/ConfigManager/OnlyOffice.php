<?php

namespace App\Services\Core\Setting\ConfigManager;

use App\Services\Core\Contracts\BootConfiguration;

class OnlyOffice implements BootConfiguration
{
    /**
     * Load SMTP||Mailtrap Configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set(
            'onlyoffice.secret',
            $configurations['secret'] ?? config('onlyoffice.secret')
        );
        
        config()->set(
            'onlyoffice.doc_server_url',
            $configurations['doc_server_url'] ?? config('onlyoffice.doc_server_url')
        );

        config()->set(
            'onlyoffice.doc_server_api_url"',
            $configurations['doc_server_api_url'] ?? config('onlyoffice.doc_server_api_url"')
        );

        config()->set(
            'onlyoffice.supported_files',
            $configurations['supported_files'] ?? config('onlyoffice.supported_files')
        );

        config()->set(
            'onlyoffice.allowed_file_size',
            $configurations['allowed_file_size'] ?? config('onlyoffice.allowed_file_size')
        );        
        
    }
}
