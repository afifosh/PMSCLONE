<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Chat\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $permission =  Permission::create([
            'name'         => 'manage_front_cms',
            'display_name' => 'Manage Front CMS',
            'guard_name'   => 'web',
        ]);
        // $role = Role::where('name', 'Admin')->first();
        // $role->givePermissionTo($permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // $role = Role::where('name', 'Admin')->first();
        // $role->revokePermissionTo('manage_front_cms');
        // Permission::where('name', 'manage_front_cms')->delete();
    }
};
