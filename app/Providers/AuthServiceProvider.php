<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    // 'App\Models\Model' => 'App\Policies\ModelPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    // $this->registerPolicies();

    //
    // Implicitly grant "Super-Admin" role all permission checks using can()
    Gate::before(function ($user, $ability) {
      if($ability === true){
        return true;
      }
      if ($user instanceof Admin && $user->id == 1) {
        return true;
      }
      // company admin (the first role from guard web)
      if ($user instanceof User && $user->hasRole(Role::COMPANY_ADMIN_ROLE)) {
        return true;
      }
    });
  }
}
