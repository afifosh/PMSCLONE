<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolesPermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */

  public function run()
  {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$this->rolesPermissions();
    // $permissionsByRole = [
    //   'admin' => ['restore posts', 'force delete posts'],
    //   'editor' => ['create a post', 'update a post', 'delete a post'],
    //   'viewer' => ['view all posts', 'view a post']
    // ];

    // $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
    //   ->map(fn ($name) => DB::table('permissions')->insertGetId(['name' => $name, 'guard_name' => 'web']))
    //   ->toArray();

    // $permissionIdsByRole = [
    //   'admin' => $insertPermissions('admin'),
    //   'editor' => $insertPermissions('editor'),
    //   'viewer' => $insertPermissions('viewer')
    // ];

    // foreach ($permissionIdsByRole as $role => $permissionIds) {
    //   $role = Role::whereName($role)->firstOrCreate(['name' => $role]);

    //   DB::table('role_has_permissions')
    //     ->insert(
    //       collect($permissionIds)->map(fn ($id) => [
    //         'role_id' => $role->id,
    //         'permission_id' => $id
    //       ])->toArray()
    //     );
    // }
  }

  public function rolesPermissions()
  {
    $permissionsByRole = [
      'Admin' => [...$this->prepPermissions(['user', 'company'])],
      'Manager' => [...$this->prepPermissions(['user'])],
    ];

    $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
      ->map(fn ($name) => Permission::firstOrCreate(['name' => $name])->id)
      ->toArray();

    $permissionIdsByRole = [];

    foreach ($permissionsByRole as $role => $p) {
      $permissionIdsByRole[$role] = $insertPermissions($role);
    }

    foreach ($permissionIdsByRole as $role => $permissionIds) {
      $role = Role::whereName($role)->firstOrCreate(['name' => $role]);

      DB::table('role_has_permissions')
        ->insert(
          collect($permissionIds)->map(fn ($id) => [
            'role_id' => $role->id,
            'permission_id' => $id
          ])->toArray()
        );
    }
  }
  public function prepPermissions($models, $crud_actions = [])
  {
    $permissions = [];
    foreach ($models as $model) {
      $permissions = [...$permissions, ...$this->crudActions($model, $crud_actions)];
    }

    return $permissions;
  }

  public function crudActions($model, $crud = [])
  {
    $actions = [];
    if (empty($crud))
      $crud = ['create', 'read', 'update', 'delete'];
    foreach ($crud as $value) {
      $actions = [...$actions, $value . ' ' . $model];
    }

    return $actions;
  }
}
