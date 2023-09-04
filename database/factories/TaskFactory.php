<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'subject' => $this->faker->word,
      'description' => $this->faker->paragraph,
      'start_date' => $this->faker->dateTime(),
      'due_date' => $this->faker->dateTime(),
      'tags' => [$this->faker->word],
      'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
      'status' => $this->faker->randomElement(['not started', 'in progress', 'on hold', 'awaiting feedback', 'completed']),
      'admin_id' => Admin::inRandomOrder()->first()->id,
    ];
  }
}
