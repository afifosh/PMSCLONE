<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $data = $this->data();

        foreach ($data as $value) {
            Permission::create([
                'name' => $value['name'],
            ]);
        }
    }

    public function data()
    {
        $data = [];
        // list of model permission
        $models = ['admin', 'role', 'permission', 'company'];

        foreach ($models as $model) {
            foreach ($this->crudActions($model) as $action) {
                $data[] = ['name' => $action];
            }
        }

        return $data;
    }

    public function crudActions($model)
    {
        $actions = [];
        // list of permission actions
        $crud = ['create', 'read', 'update', 'delete'];

        foreach ($crud as $value) {
            $actions[] = $value.' '.$model;
        }

        return $actions;
    }
}
