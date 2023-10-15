<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Contract::factory()->count(20)->create()->each(function ($contract, $index) {
      if ($contract->status != 'Draft') {
        $contract->events()->create([
          'event_type' => 'Created',
          'modifications' => $contract->toArray(),
          'description' => 'Contract Created',
        ]);
      }

      if ($contract->status == 'Active')
        ContractStage::factory()->count(2)->create([
          'contract_id' => $contract->id,
        ])->each(function ($stage, $index) use ($contract) {
          ContractPhase::factory()->count(2)->create([
            'contract_id' => $contract->id,
            'stage_id' => $stage->id,
            'estimated_cost' => $contract->value * 0.1, // 10% of contract amount
            'total_cost' => $contract->value * 0.1, // 10% of contract amount
            'start_date' => $contract->start_date->addDays($index + 5),
            'due_date' => $contract->start_date->addDays($index + 7)
          ]);
        });
    });
  }
}
