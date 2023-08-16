<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Program;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
            'category_id' => ProjectCategory::inRandomOrder()->first()->id,
            'description' => $this->faker->text,
            'start_date' => $this->faker->dateTimeBetween('-3 months', '-1 months'),
            'deadline' => $this->faker->dateTimeBetween('+1 months', '+2 months'),
            'tags' => [$this->faker->word],
            'is_progress_calculatable' => $this->faker->boolean,
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }
}
