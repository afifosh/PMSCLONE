<?php

namespace Database\Factories;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtworkFactory extends Factory
{
    protected $model = Artwork::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'year' => $this->faker->year,
            'medium_id' => null, // Define how you want to generate the 'medium_id' attribute.
            'dimension' => $this->faker->sentence,
            'description' => $this->faker->text, // Add description column
            'featured_image' => null, // Define how you want to generate the 'featured_image' attribute.
            'added_by' => null, // Define how you want to generate the 'added_by' attribute.
        ];
    }
}
