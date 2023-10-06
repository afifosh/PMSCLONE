<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractStage>
 */
class ContractStageFactory extends Factory
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
      'start_date' => '', // will be overwritten from seeder
      'due_date' => '', // will be overwritten from seeder
      'stage_amount' => '', // will be overwritten from seeder
      'description' => $this->faker->sentence,
      'is_budget_planned' => 1,
    ];
  }
}
