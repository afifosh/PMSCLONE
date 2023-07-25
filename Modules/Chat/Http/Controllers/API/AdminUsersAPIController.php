<?php

namespace Modules\Chat\Http\Controllers\API;

use Modules\Chat\Http\Controllers\AppBaseController;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Modules\Chat\Http\Requests\CreateUserRequest;
use Modules\Chat\Http\Requests\UpdateUserRequest;
use Modules\Chat\Models\User;
use Modules\Chat\Repositories\UserRepository;

/**
 * Class AdminUsersAPIController
 */
class AdminUsersAPIController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @return Response
     *
     * @throws Exception
     */
    public function index()
    {
        $users = User::with(['roles'])->orderBy('name', 'asc')->get()->except(getLoggedInUserId());
        foreach ($users as $key => $user) {
            /** @var User $user */
            $users[$key] = $user->apiObj();
        }

        return $this->sendResponse(['users' => $users], 'Users retrieved successfully.');
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  CreateUserRequest  $request
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $this->validateInput($request->all());

        $user = $this->userRepository->store($input);
        $user->roles;
        $user = $user->apiObj();

        return $this->sendResponse(['user' => $user], 'User saved successfully.');
    }

    /**
     * Display the specified User.
     *
     * @param  User  $user
     * @return Response
     */
    public function show(User $user)
    {
        $user->roles;
        $user = $user->apiObj();

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User  $user
     * @param  UpdateUserRequest  $request
     * @return Response
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $input = $request->all();
        if (isset($input['password']) && ! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        $user = $this->userRepository->update($input, $user->id);
        $user = $user->apiObj();

        return $this->sendResponse(['user' => $user], 'User updated successfully');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User  $user
     * @return Response
     *
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $this->userRepository->delete($user->id);

        return $this->sendSuccess('User deleted successfully');
    }

    /**
     * @param  User  $user
     * @return JsonResponse
     */
    public function activeDeActiveUser(User $user)
    {
        $this->userRepository->activeDeActiveUser($user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    public function validateInput($input)
    {
        if (! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['is_active'] = (! empty($input['is_active'])) ? 1 : 0;

        return $input;
    }
}
