<?php

namespace Database\Factories;

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
            'program_id' => Program::inRandomOrder()->first()->id,
            'category_id' => ProjectCategory::inRandomOrder()->first()->id,
            'description' => $this->faker->text,
            'start_date' => $this->faker->dateTime,
            'deadline' => $this->faker->dateTime,
            'tags' => [$this->faker->word],
            'is_progress_calculatable' => $this->faker->boolean,
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }
}
