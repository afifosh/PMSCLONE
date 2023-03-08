<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ApprovalLevelApprover;
use App\Models\Workflow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Workflow::factory()->create([
            'name' => 'Company Verification Workflow',
            'slug' => 'company-verification-workflow',
        ])->each(function ($workflow) {
            $workflow->levels()->createMany([
                [
                    'name' => 'Level 1 verification',
                    'order' => 1,
                ],
                [
                    'name' => 'Level 2 verification',
                    'order' => 2,
                ],
            ])->each(function ($level) {
              ApprovalLevelApprover::factory(5)->create(['approval_level_id' => $level->id]);
            });
        });


    }
}
