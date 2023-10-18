<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artwork;
use App\Models\Admin;


class ArtworkSeeder extends Seeder
{
    public function run()
    {
        // Using Eloquent to delete all rows in the Artwork table
        \App\Models\Artwork::truncate();

        // Define the number of studios you want to seed
        $studioCount = 10;

        // Find the admin with the specific email address
        // Get the first admin's ID
        $admin  = Admin::first();

      
        // Create 10 fake Artworks
        Artwork::factory($studioCount)->create([
            'added_by' => $admin->id,
        ]);     
    }
}
