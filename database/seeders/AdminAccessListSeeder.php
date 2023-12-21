<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Program;
use Illuminate\Database\Seeder;

class AdminAccessListSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $admin = Admin::find(1);
    $admin->accessiblePrograms()->attach(Program::all()->pluck('id'));
  }
}
