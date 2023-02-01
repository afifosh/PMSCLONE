<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Company::factory()->count(15)->create()->each(function ($company) {
        User::factory()->count(20)->sequence(fn ($sequence) => [
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
