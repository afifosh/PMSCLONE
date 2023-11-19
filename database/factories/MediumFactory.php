<?php

namespace Database\Factories;

use App\Models\Medium;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediumFactory extends Factory
{
    protected $model = Medium::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word, // Generate a random word as the name
        ];
    }
}
