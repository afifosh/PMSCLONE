<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CustomBladeDirectiveServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    Blade::directive('modificationAlert', function ($check) {
        return "<?php
        echo $check ? '<small class=\"text-warning\">This Field is Updated and required Approval</small>' : '';
        ?>";
    });

    Blade::directive('cMoney', function (?string $expression) {
      return "<?php echo cMoney($expression); ?>";
    });
  }
}
