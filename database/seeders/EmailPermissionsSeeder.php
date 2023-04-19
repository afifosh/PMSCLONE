<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class EmailPermissionsSeeder extends Seeder
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
          ['module' => 'Email Accounts', 'permissions' => ['Personal Mailbox', 'Shared Mailbox']],
          ['module' => 'Mailbox', 'permissions' => ['Owner', 'Reviewer', 'Editor','Contributor']],
        ]
      ];
    }
}
