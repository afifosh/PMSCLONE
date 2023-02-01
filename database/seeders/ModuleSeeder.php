<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->createModulesPermissions();
  }
  public function createModulesPermissions()
  {
    $rawData = $this->modulesPermissions();
    foreach ($rawData as $guard => $modules) {
      foreach ($modules as $module) {
        $module_id = Module::insertGetId(['name' => $module['module']]);
        DB::table('permissions')->insert(collect($module['permissions'])->map(fn ($permission) => [
          'module_id' => $module_id,
          'name' => $permission,
          'guard_name' => $guard
        ])->toArray());
      }
    }
  }
  public function modulesPermissions()
  {
    return [
      'admin' => [
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user']],
        ['module' => 'Roles Management', 'permissions' => ['read role', 'create role', 'update role', 'delete role']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
      ],
      'web' => [
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
      ],
    ];
  }
}
