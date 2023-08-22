<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'first_name' => $this->faker->firstName,
      'last_name' => $this->faker->lastName,
      'email' => $this->faker->unique()->safeEmail,
      'phone' => $this->faker->phoneNumber,
      'address' => $this->faker->address,
      'country_id' => $this->faker->numberBetween(1, 100),
      'state' => $this->faker->state,
      'zip_code' => $this->faker->postcode,
      'language' => 'en',
      'timezone' => 'Africa/Accra',
      'currency' => 'USD',
      'status' => 'Active'
    ];
  }
}
