<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApprovalLevelApprover>
 */
class ApprovalLevelApproverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'approver_id' => Admin::doesnthave('approvalLevels')->inRandomOrder()->first()->id,
        ];
    }
}
