<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
          'stage_amount' => $contract->value * 0.2, // 20% of contract value
          'start_date' => $contract->start_date->addDays($index + 5),
          'due_date' => $contract->start_date->addWeeks($index)
        ])->each(function ($stage, $index) use ($contract) {
          ContractPhase::factory()->count(2)->create([
            'contract_id' => $contract->id,
            'stage_id' => $stage->id,
            'estimated_cost' => $stage->stage_amount * 0.3, // 30% of stage amount
            'total_cost' => $stage->stage_amount * 0.3, // 30% of stage amount
            'start_date' => $stage->start_date->addDays($index + 5),
            'due_date' => $stage->due_date->addDays($index + 5)
          ]);
        });
    });
  }
}
