<?php

namespace Modules\Chat\Http\Controllers\API;

use Modules\Chat\Http\Controllers\AppBaseController;
use Exception;
use Modules\Chat\Http\Requests\CreateRoleRequest;
use Modules\Chat\Http\Requests\UpdateRoleRequest;
use Modules\Chat\Models\Role;
use Modules\Chat\Repositories\RoleRepository;

class RoleAPIController extends AppBaseController
{
    /** @var RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     *
     * @return Response
     */
    public function index()
    {
        $roles = $this->roleRepository->all();

        return $this->sendResponse($roles, 'Roles retrieved successfully');
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param  CreateRoleRequest  $request
     * @return Response
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();

        $role = $this->roleRepository->create($input);
        $role = $role->refresh();

        return $this->sendResponse(['role' => $role], 'Role saved successfully.');
    }

    /**
     * Display the specified Role.
     *
     * @param  Role  $role
     * @return Response
     */
    public function show(Role $role)
    {
        return $this->sendResponse($role, 'Role retrieved successfully');
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  Role  $role
     * @param  UpdateRoleRequest  $request
     * @return Response
     */
    public function update(Role $role, UpdateRoleRequest $request)
    {
        if ($role->is_default) {
            Flash::success('You can not edit default role.');

            return redirect(route('roles.index'));
        }
        $this->roleRepository->update($request->all(), $role->id);

        $role = $role->refresh();

        return $this->sendResponse(['role' => $role], 'Role updated successfully.');
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  Role  $role
     * @return Response
     *
     * @throws Exception
     */
    public function destroy(Role $role)
    {
        if ($role->is_default) {
            Flash::success('You can not edit default role.');

            return redirect(route('roles.index'));
        }
        $this->roleRepository->delete($role->id);

        return $this->sendSuccess('Role deleted successfully.');
    }
}
