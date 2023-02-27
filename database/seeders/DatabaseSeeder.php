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
        $this->call(ModuleSeeder::class);
        $this->call(AdminRolesPermissionsSeeder::class);
        $this->call(UserRolesPermissionsSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(HRSeeder::class);
        $this->call(CompanySeeder::class);
        // $this->call(ProgramSeeder::class);
    }
}
