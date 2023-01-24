<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name('male'),
            'email' => '', //$this->faker->unique()->safeEmail(),
            'password' => Hash::make('123456'),
            'email_verified_at' => '2022-01-02 17:04:58',
            'created_at' => now(),
        ];
    }
}
