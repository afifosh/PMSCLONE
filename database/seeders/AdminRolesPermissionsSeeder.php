<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRolesPermissionsSeeder extends Seeder
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
    $this->createAdminRoleWithAllPermissions();
    $this->createFinanceAssistantRoleWithRelatedPermissions();
    $this->createContractManagerRoleWithRelatedPermissions();
    // $permissionsByRole = [
    //   // 'Admin' => [...$this->prepPermissions(['user', 'company'])],
    //   'Additional Admin' => [...$this->prepPermissions(['user', 'company'])],
    //   'Users Manager' => [...$this->prepPermissions(['user'])],
    // ];

    // $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
    //   ->map(fn ($name) => Permission::where('name', $name)->where('guard_name', 'admin')->first()->id)
    //   ->toArray();

    // $permissionIdsByRole = [];

    // foreach ($permissionsByRole as $role => $p) {
    //   $permissionIdsByRole[$role] = $insertPermissions($role);
    // }

    // foreach ($permissionIdsByRole as $role => $permissionIds) {
    //   $role = Role::whereName($role)->firstOrCreate(['name' => $role, 'guard_name' => 'admin']);

    //   DB::table('role_has_permissions')
    //     ->insert(
    //       collect($permissionIds)->map(fn ($id) => [
    //         'role_id' => $role->id,
    //         'permission_id' => $id,
    //       ])->toArray()
    //     );
    // }
  }

  // public function prepPermissions($models, $crud_actions = [])
  // {
  //   $permissions = [];
  //   foreach ($models as $model) {
  //     $permissions = [...$permissions, ...$this->crudActions($model, $crud_actions)];
  //   }

  //   return $permissions;
  // }

  // public function crudActions($model, $crud = [])
  // {
  //   $actions = [];
  //   if (empty($crud)) {
  //     $crud = ['create', 'read', 'update', 'delete'];
  //   }
  //   foreach ($crud as $value) {
  //     $actions = [...$actions, $value . ' ' . $model];
  //   }

  //   return $actions;
  // }

  public function createAdminRoleWithAllPermissions()
  {
    $role = Role::whereName('Admin')->firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);
    $permissions = Permission::where('guard_name', 'admin')->pluck('id')->toArray();

    $role->syncPermissions($permissions);
  }

  public function createFinanceAssistantRoleWithRelatedPermissions()
  {
    $role = Role::whereName('Finance Assistant')->firstOrCreate(['name' => 'Finance Assistant', 'guard_name' => 'admin']);

    $modules = Module::where(function ($q) {
      $q->where('name', 'like', '%Invoice%')
        ->orWhere('name', 'like', '%Payment%')
        ->orWhere('name', 'like', '%Contract%');
    })
      ->pluck('id')->toArray();

    $permissions = Permission::where('guard_name', 'admin')->whereIn('module_id', $modules)->pluck('id')->toArray();

    $role->syncPermissions($permissions);
  }

  public function createContractManagerRoleWithRelatedPermissions()
  {
    $role = Role::whereName('Contract Manager')->firstOrCreate(['name' => 'Contract Manager', 'guard_name' => 'admin']);

    $modules = Module::where(function ($q) {
      $q->whereIn('name', ['Programs', 'Company Management'])
        ->orWhere('name', 'like', '%Contract%');
    })
      ->pluck('id')->toArray();

    $permissions = Permission::where('guard_name', 'admin')->whereIn('module_id', $modules)->pluck('id')->toArray();

    $role->syncPermissions($permissions);
  }
}
