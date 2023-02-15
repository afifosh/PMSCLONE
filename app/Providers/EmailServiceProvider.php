<?php

namespace App\Providers;

use App\Models\EmailService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $configurations = cache()->store(config('cache.default'))->get('project_email_configurations');

        // if configurations are not present, return & use default ones in app .env
        if (is_null($configurations)) {
            return;
        }

        $configurations = json_decode($configurations);

        $config = array(
            'transport'  => $configurations->transport ?? config('mail.mailers.smtp.transport'),
            'host'       => $configurations->host ?? config('mail.mailers.smtp.host'),
            'port'       => $configurations->port ?? config('mail.mailers.smtp.port'),
            'encryption' => $configurations->encryption ?? config('mail.mailers.smtp.encryption'),
            'username'   => $configurations->username ?? config('mail.mailers.smtp.username'),
            'password'   => $configurations->password ?? config('mail.mailers.smtp.password'),
            'timeout'    => $configurations->timeout ?? config('mail.mailers.smtp.timeout'),
        );

        config()->set('mail.mailers.smtp', $config);
        config()->set('mail.from.address', $configurations->sent_from_email ?? config('mail.from.address'));
        config()->set('mail.from.name', $configurations->sent_from_name ?? config('mail.from.name'));
    }
}
