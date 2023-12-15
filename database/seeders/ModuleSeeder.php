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
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user', 'impersonate user']],
        ['module' => 'Roles Management', 'permissions' => ['read role', 'create role', 'update role', 'delete role']],
        ['module' => 'Company Roles', 'permissions' => ['read company role', 'create company role', 'update company role', 'delete company role']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
        ['module' => 'Programs ACL Rule', 'permissions' => ['read acl', 'create acl', 'update acl', 'delete acl']],
        ['module' => 'Programs', 'permissions' => ['read program', 'create program', 'update program', 'delete program']],
        ['module' => 'Contracts', 'permissions' => ['read contract', 'create contract', 'update contract', 'delete contract']],
        ['module' => 'Invoices', 'permissions' => ['read invoice', 'create invoice', 'update invoice', 'delete invoice']],
        ['module' => 'Payments', 'permissions' => ['read payment', 'create payment', 'update payment', 'delete payment']],
      ],
      'web' => [
        ['module' => 'User Management', 'permissions' => ['read user', 'create user', 'update user', 'delete user']],
        ['module' => 'Roles', 'permissions' => ['read role']],
        ['module' => 'Company Management', 'permissions' => ['read company', 'create company', 'update company', 'delete company']],
      ],
    ];
  }
}
