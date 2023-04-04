<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::factory()->count(10)->sequence(fn ($sequence) => ['email' => 'admin'.$sequence->index + 1 .'@example.com'])->create()->each(function ($admin) {
            $admin->assignRole(Role::where('guard_name', 'admin')->inRandomOrder()->first());
        });
    }
}
