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
    // Tax::factory()->count(5)->create();
    // Tax::factory()->count(5)->create(['is_retention' => true]);

        // No Tax 0%
        Tax::create([
          'name' => 'No Tax',
          'type' => 'Percent',
          'amount' => 0,
          'status' => 'Active',
          'is_retention' => false,
      ]);

      // 5% Tax
      Tax::create([
          'name' => '5% Tax',
          'type' => 'Percent',
          'amount' => 5,
          'status' => 'Active',
          'is_retention' => false,
      ]);

      // 15% Tax
      Tax::create([
          'name' => '15% Tax',
          'type' => 'Percent',
          'amount' => 15,
          'status' => 'Active',
          'is_retention' => false,
      ]);

 
       // 15% Tax
       Tax::create([
        'name' => '5% With Holding Tax',
        'type' => 'Percent',
        'amount' => -5,
        'status' => 'Active',
        'is_retention' => false,
    ]);     
 
      // 5% Retention
      Tax::create([
        'name' => '5% Retention',
        'type' => 'Percent',
        'amount' => 5,
        'status' => 'Active',
        'is_retention' => true,  // Assuming this column exists in your Tax model
      ]);


  }
}
