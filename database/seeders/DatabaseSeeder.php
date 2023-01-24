<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        // \App\Models\User::factory(30)->create();
        $this->call(
            AdminRolesPermissionsSeeder::class,
            UserRolesPermissionsSeeder::class
        );
        Admin::factory()->count(5)->sequence(fn ($sequence) => ['email' => 'admin'.$sequence->index + 1 .'@example.com'])->create()->each(function ($admin) {
            $admin->assignRole(Role::where('guard_name', 'admin')->inRandomOrder()->first());
        });
        Company::factory()->count(5)->create()->each(function ($company) {
            User::factory()->count(10)->sequence(fn ($sequence) => [
                'email' => 'user'.$sequence->index + 1 .'@comp'.$company->id.'.com',
                'company_id' => $company->id])->create()->each(function ($user) {
                  if (substr($user, 3, 1) == 1) {
                      $user->assignRole(Role::where('guard_name', 'web')->first());
                  } else {
                      $user->assignRole(Role::where('guard_name', 'web')->inRandomOrder()->first());
                  }
              });
        });
    }
}
