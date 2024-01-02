<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use App\Models\PhaseTax;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Contract::factory()->count(20)->create()->each(function ($contract, $index) {
      if ($contract->getRawOriginal('status') != '0') {
        $contract->events()->create([
          'event_type' => 'Created',
          'modifications' => $contract->toArray(),
          'description' => 'Contract Created',
        ]);
      }

      if ($contract->getRawOriginal('status') == '1')
        ContractStage::factory()->count(10)->create([
          'contract_id' => $contract->id,
        ])->each(function ($stage, $index) use ($contract) {
          ContractPhase::factory()->count(10)->create([
            'contract_id' => $contract->id,
            'stage_id' => $stage->id,
            'estimated_cost' => $contract->value * 0.1, // 10% of contract amount
            'total_cost' => $contract->value * 0.1, // 10% of contract amount
            'start_date' => $contract->start_date->addDays($index + 5),
            'due_date' => $contract->start_date->addDays($index + 7)
          ])->each(function ($phase, $index) {
            PhaseTax::factory()->count(5)->create([
              'contract_phase_id' => $phase->id,
            ]);
            $phase->reCalculateTaxAmountsAndResetManualAmounts(false);
            $phase->reCalculateTotal();
          });
        });
    });
  }
}
