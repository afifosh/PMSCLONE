<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\ApplicationPipeline;
use App\Models\ApplicationScoreCard;
use App\Models\ApplicationType;
use App\Models\Company;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
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
      'company_id' => Company::inRandomOrder()->first()->id,
      'program_id' => Program::inRandomOrder()->first()->id,
      'type_id' => ApplicationType::inRandomOrder()->first()->id,
      'category_id' => ApplicationCategory::inRandomOrder()->first()->id,
      'pipeline_id' => ApplicationPipeline::inRandomOrder()->first()->id,
      'scorecard_id' => ApplicationScoreCard::inRandomOrder()->first()->id,
      'form_id' => '',
      'start_at' => now(),
      'end_at' => now()->addDays(30),
      'description' => $this->faker->sentence,
    ];
  }
}
