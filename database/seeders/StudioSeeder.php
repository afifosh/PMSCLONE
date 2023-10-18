<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Studio;
use App\Models\Admin;

class StudioSeeder extends Seeder
{
    public function run()
    {
        // Define the number of studios you want to seed
        $studioCount = 10;

        // Find the admin with the specific email address
        // Get the first admin's ID
        $admin = Admin::first();

        // Create and insert studios using the factory, setting added_by to the admin's ID
        Studio::factory($studioCount)->create([
            'added_by' => $admin->id,
        ]);
    }
}
