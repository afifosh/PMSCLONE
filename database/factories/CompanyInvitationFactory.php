<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyInvitation>
 */
class CompanyInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'token' => $this->faker->unique()->uuid(),
            'role_id' => Role::where('guard_name', 'web')->inRandomOrder()->first()->id,
            'valid_till' => now()->addMonth(),
            'status' => 'sent',
        ];
    }
}
