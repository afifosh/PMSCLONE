<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
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
    if (request()->segment(1) === 'admin') {
      $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalAdminMenu.json'));
      $verticalMenuData = json_decode($verticalMenuJson);
      $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalAdminMenu.json'));
      $horizontalMenuData = json_decode($horizontalMenuJson);
    } else {
      $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
      $verticalMenuData = json_decode($verticalMenuJson);
      $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
      $horizontalMenuData = json_decode($horizontalMenuJson);
    }

    // Share all menuData to all the views
    \View::share('menuData', [$verticalMenuData, $horizontalMenuData]);
  }
}
