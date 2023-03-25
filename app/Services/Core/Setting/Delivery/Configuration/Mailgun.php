<?php

namespace App\Services\Core\Setting\Delivery\Configuration;

use App\Services\Core\Contracts\BootConfiguration;

class Mailgun implements BootConfiguration
{
    /**
     * Loads mailgun configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set(
            'services.mailgun.domain',
            $configurations['domain_name'] ?? config('services.mailgun.domain')
        );

        config()->set(
            'services.mailgun.secret',
            $configurations['api_key'] ?? config('services.mailgun.secret')
        );
    }
}
