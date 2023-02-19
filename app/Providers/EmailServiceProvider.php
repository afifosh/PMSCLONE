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

        if (is_null($configurations)) {
            $this->cacheMailService();
        }

        $configurations = json_decode($configurations);

        $this->chargeUpMailConfig($configurations);
    }

    /**
     * Cache email configurations if they are not in cache already
     * 
     * @return void
     */
    private function cacheMailService(): void
    {
        if (Schema::hasTable('email_services')) {
            $service = EmailService::query()->whereIsActive(true)->first();
        }

        if (! isset($service) || ! $service) {
            return;
        }

        $configurations = array(
            'transport'         => $service->transport,
            'host'              => $service->host,
            'port'              => $service->port,
            'encryption'        => $service->encryption,
            'username'          => $service->username,
            'password'          => $service->password,
            'sent_from_address' => $service->sent_from_address,
            'sent_from_name'    => $service->sent_from_name,
            'access_key_id'     => $service->access_key_id,
            'secret_access_key' => $service->secret_access_key,
            'region'            => $service->region,
            'domain_name'       => $service->domain_name,
            'api_key'           => $service->api_key,
        );

        // store all email settings in cache
        cache()->store(config('cache.default'))->put(
            'project_email_configurations',
            json_encode($configurations)
        );
    }

    /**
     * Charge up mail configurations 
     * 
     * @param $configurations
     * @return void
     */
    private function chargeUpMailConfig($configurations): void
    {
        $defaults = array(
            'transport'  => $configurations->transport ?? config('mail.mailers.smtp.transport'),
            'host'       => $configurations->host ?? config('mail.mailers.smtp.host'),
            'port'       => $configurations->port ?? config('mail.mailers.smtp.port'),
            'encryption' => $configurations->encryption ?? config('mail.mailers.smtp.encryption'),
            'username'   => $configurations->username ?? config('mail.mailers.smtp.username'),
            'password'   => $configurations->password ?? config('mail.mailers.smtp.password'),
        );

        // sent from address & name
        config()->set('mail.from.name', $configurations->sent_from_name ?? config('mail.from.name'));
        config()->set('mail.from.address', $configurations->sent_from_email ?? config('mail.from.address'));

        // smtp configurations
        config()->set('mail.mailers.smtp', $defaults);

        // aws configurations - if present
        $this->chargeUpAwsConfig($configurations);

        // mailgun configurations - if present
        $this->chargeUpMailgunConfig($configurations);
    }

    /**
     * Charge up AWS email service
     * 
     * @param $configurations
     */
    private function chargeUpAwsConfig($configurations)
    {
        $aws = array(
            'transport'         => 'ses',
            'access_key_id'     => $configurations->access_key_id ?? config('mail.mailers.ses.access_key_id'),
            'secret_access_key' => $configurations->secret_access_key ?? config('mail.mailers.ses.secret_access_key'),
            'region'            => $configurations->region ?? config('mail.mailers.ses.region'),
        );

        config()->set('mail.mailers.ses', $aws);
    }

    /**
     * Charge up mailgun config
     * 
     * @param $configurations
     */
    private function chargeUpMailgunConfig($configurations)
    {
        $mailgun = array(
            'transport'   => 'mailgun',
            'domain_name' => $configurations->domain_name ?? config('mail.mailers.mailgun.domain_name'),
            'api_key'     => $configurations->api_key ?? config('mail.mailers.mailgun.api_key'),
        );

        config()->set('mail.mailers.ses', $mailgun);
    }
}
