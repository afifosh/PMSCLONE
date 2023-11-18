<?php

namespace Database\Seeders;
use App\Models\InvoiceConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // InvoiceConfig::factory()->count(5)->create();

        // No Tax 0%
        InvoiceConfig::create([
          'name' => 'No Tax',
          'type' => 'Percent',
          'amount' => 0,
          'status' => 'Active',
          'config_type' => 'Tax',
      ]);

      // 5% Tax
      InvoiceConfig::create([
          'name' => '5% Tax',
          'type' => 'Percent',
          'amount' => 5,
          'status' => 'Active',
          'config_type' => 'Tax',
      ]);

      // 15% Tax
      InvoiceConfig::create([
          'name' => '15% Tax',
          'type' => 'Percent',
          'amount' => 15,
          'status' => 'Active',
          'config_type' => 'Tax',
      ]);

      // 5% Retention
      InvoiceConfig::create([
        'name' => '5% Retention',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'config_type' => 'Retention',
      ]);


      // 5% Down Payment
      InvoiceConfig::create([
        'name' => '5% Downpayment',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'config_type' => 'Down Payment',
      ]);
  }
}
