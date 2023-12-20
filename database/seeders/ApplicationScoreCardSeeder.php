<?php

namespace Database\Seeders;

use App\Models\ApplicationScoreCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationScoreCardSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    ApplicationScoreCard::factory()->count(10)->create();
  }
}
