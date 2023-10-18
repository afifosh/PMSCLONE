<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Studio;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Studio>
 */

class StudioFactory extends Factory
{
    protected $model = Studio::class;

    public function definition()
    {
        return [
            'added_by' => null, // You can customize this based on your needs
            'country_id' => null, // You can customize this based on your needs
            'state_id' => null, // You can customize this based on your needs
            'city_id' => null, // You can customize this based on your needs
            'name' => $this->faker->unique()->company,
            'website' => $this->faker->unique()->url,
            'avatar' => null, // You can customize this based on your needs
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'zip' => $this->faker->postcode,
            'phone' => $this->faker->phoneNumber,
            'language' => 'en', // Default language
            'timezone' => null, // You can customize this based on your needs
            'currency' => null, // You can customize this based on your needs
            'status' => $this->faker->randomElement(['active', 'disabled', 'pending']),
        ];
    }
}
