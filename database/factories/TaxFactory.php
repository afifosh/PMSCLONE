<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->word,
      'type' => $this->faker->randomElement(['Percent', 'Fixed']),
      'amount' => $this->faker->numberBetween(1, 100),
      'status' => $this->faker->randomElement(['Active', 'Inactive']),
    ];
  }
}
