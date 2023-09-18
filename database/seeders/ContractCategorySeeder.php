<?php

namespace Database\Seeders;

use App\Models\ContractCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    ContractCategory::factory()->count(10)->create();
  }
}
