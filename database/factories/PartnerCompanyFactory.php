<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerCompany>
 */
class PartnerCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->domainName(),
            'website' => $this->faker->unique()->domainName(),
            'phone' => $this->faker->phoneNumber()
        ];
    }
}
