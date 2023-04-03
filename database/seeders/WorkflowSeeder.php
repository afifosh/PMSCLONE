<?php

namespace Database\Seeders;

use App\Models\ApprovalLevelApprover;
use App\Models\Workflow;
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
        ['name' => 'Level 1 verification'],
        ['name' => 'Level 2 verification']
      ])->each(function ($level) {
        ApprovalLevelApprover::factory(1)->create(['workflow_level_id' => $level->id, 'user_id' => 1]);
        ApprovalLevelApprover::factory(2)->create(['workflow_level_id' => $level->id]);
      });
    });
  }
}
