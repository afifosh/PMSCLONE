<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use App\Models\ContractType;
use App\Models\Program;
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
    $endDate = $this->faker->boolean(90);
    $isDraft = $this->faker->boolean(30);
    $value = rand(1000, 10000);
    return [
      'type_id' => ContractType::inRandomOrder()->first()->id,
      'project_id' => $this->faker->boolean() ?  Project::inRandomOrder()->first()->id : null,
      'program_id' => Program::inRandomOrder()->first()->id,
      'assignable_type' => Company::class, //$assignToNone ? null : ($assignToCompany ? Company::class : Client::class),
      'assignable_id' =>  Company::inRandomOrder()->first()->id, // $assignToNone? null : ($assignToCompany ? Company::inRandomOrder()->first()->id : Client::inRandomOrder()->first()->id),
      'refrence_id' => $this->faker->unique()->sentence(),
      'subject' => $this->faker->sentence(),
      'value' => $value,
      'start_date' => $isDraft ? null : $this->faker->dateTimeBetween('-2 years', '-1 years'),
      'end_date' => $endDate ? $this->faker->dateTimeBetween('now', '2 years') : null,
      'description' => $this->faker->paragraph(),
      'status' => $isDraft ? 'Draft' : $this->faker->randomElement(['Active', 'Draft', 'Terminated'])
    ];
  }
}
