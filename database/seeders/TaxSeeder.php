<?php

namespace Database\Seeders;
use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $taxes = [
      ['name' => '0% VAT', 'amount' => 0, 'type' => 'Percent'],
      ['name' => '10% VAT', 'amount' => 10, 'type' => 'Percent'],
      ['name' => '15% VAT', 'amount' => 15, 'type' => 'Percent'],
    ];
    foreach ($taxes as $tax) {
      Tax::create($tax);
    }
    // Tax::factory()->count(5)->create();
    Tax::factory()->count(5)->create(['is_retention' => true]);
  }
}
