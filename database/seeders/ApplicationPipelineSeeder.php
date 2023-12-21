<?php

namespace Database\Seeders;

use App\Models\ApplicationPipeline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationPipelineSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    ApplicationPipeline::factory()->count(10)->create()->each(function (ApplicationPipeline $pipeline) {
      $pipeline->stages()->createMany([
        [
          'name' => 'Application',
          'is_default' => true,
        ],
        [
          'name' => 'Review',
        ],
        [
          'name' => 'Interview',
        ],
        [
          'name' => 'Offer',
        ],
        [
          'name' => 'Hired',
        ],
        [
          'name' => 'Rejected',
        ],
      ]);
    });
  }
}
