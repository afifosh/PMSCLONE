<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskFollower>
 */
class TaskFollowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => \App\Models\Task::inRandomOrder()->first()->id,
            'admin_id' => \App\Models\Admin::inRandomOrder()->first()->id,
        ];
    }
}
