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
        if (Schema::hasTable('email_services')) {
            $service = EmailService::where('is_active', true)->first();
        }

        if (! isset($service) || ! $service) {
            return;
        }

        $fields = $service->emailServiceFields()->pluck('field_value', 'field_name')->toArray();

        config([
            'mail.default' => $service->service ?? config('mail.default'),
            'mail.mailers.smtp.host' => $fields->host ?? config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $fields->port ?? config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $fields->username ?? config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $fields->password ?? config('mail.mailers.smtp.password'),
            'mail.mailers.smtp.encryption' => $fields->encryption_key ?? config('mail.mailers.smtp.encryption'),
            'mail.from.address' => $fields->email_sent_from_email ?? config('mail.from.address'),
            'mail.from.name' => $fields->email_sent_from_name ?? config('mail.from.name'),
        ]);
    }
}
