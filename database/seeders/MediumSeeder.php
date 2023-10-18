<?php

namespace Database\Seeders;
use App\Models\Medium;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
class MediumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Using Eloquent to delete all rows in the Artwork table
        \App\Models\Medium::truncate();

        // Define the number of studios you want to seed
        $studioCount = 10;

        // Find the admin with the specific email address
        // Get the first admin's ID
        $admin  = Admin::first();

      
        // Create 10 fake Artworks
        Medium::factory($studioCount)->create([
            'added_by' => $admin->id,
        ]);     
    }
}
