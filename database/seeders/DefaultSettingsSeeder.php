<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Services\Core\Setting\SettingService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $settings = [
      'contract-notifications' => [
        'enable_notifications' => 1,
        'cycle_count' => 4,
        'cycle_unit_name' => 'Months',
        'cycle_unit_value' => 1,
        'emails' => Admin::InRandomOrder()->take(3)->pluck('id')->toArray(),
      ],
    ];

    $settingService = resolve(SettingService::class);

    foreach ($settings as $key => $value) {
      $settingService->seedData($key, $value);
    }
  }
}
