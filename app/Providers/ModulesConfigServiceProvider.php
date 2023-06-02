<?php

namespace App\Providers;

use App\Helpers\CRM\Traits\SetOauthGoogleConfig;
use App\Helpers\CRM\Traits\SetOauthMicrosoftConfig;
use Illuminate\Support\ServiceProvider;

class ModulesConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
      try {
      SetOauthMicrosoftConfig::new(true)
        ->cashClear()
        ->configSet();
    } catch (\Exception $exception) {
    }

    try {
      SetOauthGoogleConfig::new(true)
        ->cashClear()
        ->configSet();
    } catch (\Exception $exception) {
    }
    }
}
