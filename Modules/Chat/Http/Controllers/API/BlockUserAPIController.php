<?php

namespace Modules\Chat\Http\Controllers\API;

use Modules\Chat\Http\Controllers\AppBaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Models\BlockedUser;
use Modules\Chat\Models\User;
use Modules\Chat\Repositories\BlockUserRepository;

/**
 * Class BlockUserController
 */
class BlockUserAPIController extends AppBaseController
{
    /**
     * @var BlockUserRepository
     */
    private $blockUserRepository;

    public function __construct(BlockUserRepository $blockUserRepository)
    {
        $this->blockUserRepository = $blockUserRepository;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function blockUnblockUser(Request $request)
    {
        $input = $request->all();

        $input['blocked_by'] = Auth::id();
        $input['is_blocked'] = ($input['is_blocked'] == 'false') ? false : true;
        $blockText = ($input['is_blocked'] == true) ? 'blocked' : 'unblocked';
        $blockedTo = User::findOrFail($input['blocked_to']);

        $this->blockUserRepository->blockUnblockUser($input);

        return $this->sendResponse(['user' => $blockedTo], "User $blockText successfully.");
    }

    /**
     * @return mixed
     */
    public function blockedUsers()
    {
        [$blockedUserIds, $blockByMeUsers] = $this->blockUserRepository->blockedUserIds();

        return $this->sendResponse(
            ['users_ids' => $blockedUserIds], 'Blocked users retrieved successfully.'
        );
    }

    /**
     * @return JsonResponse
     */
    public function blockUsersByMe()
    {
        $blockedUserIds = BlockedUser::whereBlockedBy(Auth::id())->pluck('blocked_to')->toArray();

        $users = User::whereIn('id', $blockedUserIds)
            ->select(['id', 'name', 'photo_url', 'gender', 'is_online'])
            ->get();

        return $this->sendResponse(
            ['users' => $users], 'Blocked users retrieved successfully.'
        );
    }
}
