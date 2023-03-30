<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Permissions;

use App\Innoclapps\Models\Role;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\RoleRepository;

class RoleRepositoryEloquent extends AppRepository implements RoleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Role::class;
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        // Use Spatie model create method
        // as it's checking for the defined guard
        $model = new Role;
        $role  = $model->create($attributes);

        $role->givePermissionTo($attributes['permissions'] ?? []);

        return $role;
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $data
     * @param $id
     *
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $role = parent::update($data, $id);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }
}
