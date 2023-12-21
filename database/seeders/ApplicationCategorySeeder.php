<?php

namespace Database\Seeders;

use App\Models\ApplicationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    ApplicationCategory::factory()->count(10)->create();
  }
}
