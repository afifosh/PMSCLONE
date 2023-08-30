<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractPhase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Contract::factory()->count(20)->create()->each(function ($contract) {

      ContractPhase::factory()->count(1)->create([
        'contract_id' => $contract->id,
        'estimated_cost' => $contract->value - 100,
        'start_date' => $contract->start_date ? $contract->start_date : now(),
        'due_date' => $contract->start_date ?  $contract->start_date->addDays(20) : now()->addDays(20)
      ]);
    });
  }
}
