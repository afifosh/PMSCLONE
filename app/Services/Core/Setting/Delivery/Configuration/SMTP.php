<?php

namespace App\Services\Core\Setting\Delivery\Configuration;

use App\Services\Core\Contracts\BootConfiguration;

class SMTP implements BootConfiguration
{
    /**
     * Load SMTP||Mailtrap Configurations
     * 
     * @param $configurations
     */
    public function load($configurations)
    {
        config()->set(
            'mail.mailers.smtp.host',
            $configurations['host'] ?? config('mail.mailers.smtp.host')
        );

        config()->set(
            'mail.mailers.smtp.port',
            $configurations['port'] ?? config('mail.mailers.smtp.port')
        );

        config()->set(
            'mail.mailers.smtp.encryption',
            $configurations['encryption'] ?? config('mail.mailers.smtp.encryption')
        );

        config()->set(
            'mail.mailers.smtp.username',
            $configurations['username'] ?? config('mail.mailers.smtp.username')
        );

        config()->set(
            'mail.mailers.smtp.password',
            $configurations['password'] ?? config('mail.mailers.smtp.password')
        );
    }
}
