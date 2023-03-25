<?php

namespace App\Services\Core\Setting\Delivery\Configuration;

use App\Services\Core\Contracts\BootConfiguration;

class Amazon implements BootConfiguration
{
    /**
     * Loads amazon ses configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set('mail.default', 'ses');

        config()->set(
            'services.ses.key',
            $configurations['access_key_id'] ?? config('services.ses.key')
        );
        config()->set(
            'services.ses.secret',
            $configurations['secret_access_key'] ?? config('services.ses.secret')
        );

        config()->set(
            'services.ses.region',
            $configurations['region'] ?? config('services.ses.region')
        );
    }
}
