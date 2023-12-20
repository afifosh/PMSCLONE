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

      // 5% Tax
      InvoiceConfig::create([
          'name' => '5% Tax',
          'type' => 'Percent',
          'amount' => 5,
          'status' => 'Active',
          'config_type' => 'Tax',
          'category' => 1 // Value Added Tax
      ]);

      // 15% Tax
      InvoiceConfig::create([
          'name' => '15% Tax',
          'type' => 'Percent',
          'amount' => 15,
          'status' => 'Active',
          'config_type' => 'Tax',
          'category' => 1 // Value Added Tax
      ]);

      // 5% Retention
      InvoiceConfig::create([
        'name' => '5% Retention',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'config_type' => 'Retention',
      ]);

      // 10% Retention
      InvoiceConfig::create([
        'name' => '10% Retention',
        'type' => 'Percent',
        'amount' => 10,
        'status' => 'Active',
        'config_type' => 'Retention',
      ]);

      // 15% Retention
      InvoiceConfig::create([
        'name' => '15% Retention',
        'type' => 'Percent',
        'amount' => 15,
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

      // 5% Reverse Charge Tax
      InvoiceConfig::create([
        'name' => '5% Reverse Charge',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'config_type' => 'Tax',
        'category' => 3, // Reverse Charge Tax
      ]);

      // 10% Reverse Charge Tax
      InvoiceConfig::create([
        'name' => '10% Reverse Charge',
        'type' => 'Percent',
        'amount' => 10,
        'status' => 'Active',
        'config_type' => 'Tax',
        'category' => 3, // Reverse Charge Tax
      ]);

      // 15% Reverse Charge Tax
      InvoiceConfig::create([
        'name' => '15% Reverse Charge',
        'type' => 'Percent',
        'amount' => 15,
        'status' => 'Active',
        'config_type' => 'Tax',
        'category' => 3, // Reverse Charge Tax
      ]);      

      // 5% Withholding Tax
      InvoiceConfig::create([
        'name' => '5% Withholding Tax',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'config_type' => 'Tax',
        'category' => 2, // Withholding Tax
      ]);

      // 10% Withholding Tax
      InvoiceConfig::create([
        'name' => '10% Withholding Tax',
        'type' => 'Percent',
        'amount' => 10,
        'status' => 'Active',
        'config_type' => 'Tax',
        'category' => 2,  // Withholding Tax
      ]);
  }
}
