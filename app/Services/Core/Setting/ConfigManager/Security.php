<?php

namespace App\Services\Core\Setting\ConfigManager;

use App\Services\Core\Contracts\BootConfiguration;

class Security implements BootConfiguration
{
    /**
     * Load SMTP||Mailtrap Configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set(
            'auth.timeout_warning_seconds',
            $configurations['timeout_warning_seconds'] ?? config('auth.timeout_warning_seconds')
        );
        
        config()->set(
            'auth.timeout_after_seconds',
            $configurations['timeout_after_seconds'] ?? config('auth.timeout_after_seconds')
        );
    }
}
