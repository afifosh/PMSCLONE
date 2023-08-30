<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use App\Models\ContractType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $assignToCompany = $this->faker->boolean();
    $assignToNone = $this->faker->boolean();
    $startdate = $this->faker->boolean(90);
    return [
      'type_id' => ContractType::inRandomOrder()->first()->id,
      'project_id' => $this->faker->boolean() ?  Project::inRandomOrder()->first()->id : null,
      'assignable_type' => $assignToNone ? null : ($assignToCompany ? Company::class : Client::class),
      'assignable_id' => $assignToNone? null : ($assignToCompany ? Company::inRandomOrder()->first()->id : Client::inRandomOrder()->first()->id),
      'refrence_id' => $this->faker->unique()->sentence(),
      'subject' => $this->faker->sentence(),
      'value' => rand(1000, 10000),
      'start_date' => $startdate ? $this->faker->dateTimeBetween('-2 years', '-1 years') : null,
      'end_date' => $startdate ? $this->faker->dateTimeBetween('now', '2 years') : null,
      'description' => $this->faker->paragraph(),
      'status' => $this->faker->randomElement(['Active', 'Draft', 'Terminated'])
    ];
  }
}
