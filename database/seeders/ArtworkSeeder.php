<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artwork;
use App\Models\Admin;
use App\Models\Medium; // Import the Medium model

class ArtworkSeeder extends Seeder
{
    public function run()
    {
        // Using Eloquent to delete all rows in the Artwork table
        Artwork::query()->delete();

        // Define the number of artworks you want to seed
        $artworkCount = 10;

        // Find the admin with the specific email address
        // Get the first admin's ID
        $admin = Admin::first();

        // Get a list of all available mediums
        $mediums = Medium::all();

        // Create 10 fake Artworks with a random medium_id if mediums exist
        Artwork::factory($artworkCount)->create([
            'added_by' => $admin->id,
            'medium_id' => $mediums->isEmpty() ? null : $mediums->random()->id, // Assign a random medium_id if mediums exist, otherwise set to null
        ]);
    }
}
