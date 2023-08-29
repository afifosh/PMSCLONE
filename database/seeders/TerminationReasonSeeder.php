<?php

namespace Database\Seeders;

use App\Models\TerminationReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TerminationReasonSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    TerminationReason::insert([
      ['name' => 'Termination by Mutual Agreement'],
      ['name' => 'Termination Due to Breach'],
      ['name' => 'Termination Based on Contractual Terms'],
      ['name' => 'Termination Due to External Factors']
    ]);
  }
}
