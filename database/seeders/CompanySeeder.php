<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role as ModelsRole;
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
      Company::factory()->count(12)->create()->each(function ($company) {
        User::factory()->count(7)->sequence(fn ($sequence) => [
            'email' => 'user'.$sequence->index + 1 .'@comp'.$company->id.'.com',
            'company_id' => $company->id])->create()->each(function ($user) {
              if (substr($user->email, 4, 2) == '1@') {
                  $user->assignRole(Role::where('name', ModelsRole::COMPANY_ADMIN_ROLE)->first()->name);
              } else {
                  $user->assignRole(Role::where('guard_name', 'web')->where('name', '!=', ModelsRole::COMPANY_ADMIN_ROLE)->inRandomOrder()->first());
              }
          });
    });
    }
}
