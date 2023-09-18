<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractMilestone>
 */
class ContractMilestoneFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->sentence(),
      'description' => $this->faker->sentence(),
      'estimated_cost' => 0,
      'start_date' => now(),
      'due_date' => now(),
    ];
  }
}
