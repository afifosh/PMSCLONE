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
            $email_service = EmailService::where('is_active', true)->first();
        }

        if (! isset($email_service) || ! $email_service) {
            return;
        }

        
    }
}
