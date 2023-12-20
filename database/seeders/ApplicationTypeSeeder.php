<?php

namespace Database\Seeders;

use App\Models\ApplicationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    ApplicationType::factory()->count(10)->create();
  }
}
