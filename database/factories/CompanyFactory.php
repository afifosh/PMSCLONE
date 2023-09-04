<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'added_by' => Admin::inRandomOrder()->first()->id,
            'name' => $this->faker->unique()->company(),
            'website' => $this->faker->unique()->domainName(),
            'email' => $this->faker->safeEmail(),
            'status' => 'active',
        ];
    }
}
