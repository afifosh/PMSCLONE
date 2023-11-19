<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      // 'name' => $this->faker->name,
      'address' => $this->faker->address,
      'latitude' => $this->faker->latitude,
      'longitude' => $this->faker->longitude,
      'zoomLevel' => $this->faker->numberBetween(1, 20),
      'country_id' => $this->faker->numberBetween(1, 20),
      'city_id' => $this->faker->numberBetween(1, 20),
      'state_id' => $this->faker->numberBetween(1, 20)
    ];
  }
}
