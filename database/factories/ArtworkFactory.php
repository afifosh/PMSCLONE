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
            'year' => $this->faker->year,
            'medium' => $this->faker->word,
            'dimension' => $this->faker->sentence,
            'title' => $this->faker->sentence,
            'featured_image' => null, // You can generate fake image URLs here if needed.
            'added_by' => null, // Define how you want to generate the 'added_by' attribute.
        ];
    }
}
