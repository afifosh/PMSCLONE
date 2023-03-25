<?php

namespace App\Services\Core\Setting\ConfigManager;

use App\Services\Core\Contracts\BootConfiguration;

class General implements BootConfiguration
{
    /**
     * Load SMTP||Mailtrap Configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set(
            'app.name',
            $configurations['company_name'] ?? config('app.name')
        );
        
        config()->set(
            'app.timezone',
            $configurations['timezone'] ?? config('app.timezone')
        );
    }
}
