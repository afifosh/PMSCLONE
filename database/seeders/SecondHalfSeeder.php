<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecondHalfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->call(HRSeeder::class);
      $this->call(CompanySeeder::class);
      $this->call(ProgramSeeder::class);
      $this->call(WorkflowSeeder::class);
      $this->call(KycDocumentSeeder::class);
    }
}
