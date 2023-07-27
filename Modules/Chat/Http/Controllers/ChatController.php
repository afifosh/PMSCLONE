<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Modules\Chat\Models\Setting;
use Modules\Chat\Models\User;
use Modules\Chat\Repositories\BlockUserRepository;
use Modules\Chat\Repositories\UserRepository;

/**
 * Class ChatController
 */
class ChatController extends AppBaseController
{
    /**
     * Show the application dashboard.
     *
     * @param  Request  $request
     * @return Factory|Application|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $data['conversationType'] = 'general';
        $conversationId = $request->get('conversationId');
        $data['conversationId'] = ! empty($conversationId) ? $conversationId : 0;

        /** @var UserRepository $userRepository */
        $userRepository = app(UserRepository::class);
        /** @var BlockUserRepository $blockUserRepository */
        $myContactIds = $userRepository->myContactIds();

        /** @var BlockUserRepository $blockUserRepository */
        $blockUserRepository = app(BlockUserRepository::class);
        [$blockUserIds, $blockedByMeUserIds] = $blockUserRepository->blockedUserIds();

        $data['users'] = User::toBase()
            ->limit(50)
            ->orderBy('email')
            ->select(['email', 'id'])
            ->pluck('email', 'id')
            ->except(getLoggedInUserId());
        $data['enableGroupSetting'] = isGroupChatEnabled();
        $data['membersCanAddGroup'] = canMemberAddGroup();
        $data['myContactIds'] = $myContactIds;
        $data['blockUserIds'] = $blockUserIds;
        $data['blockedByMeUserIds'] = $blockedByMeUserIds;

        /** @var Setting $setting */
        $setting = Setting::where('key', 'notification_sound')->pluck('value', 'key')->toArray();
        if (isset($setting['notification_sound'])) {
            $data['notification_sound'] = app(Setting::class)->getNotificationSound($setting['notification_sound']);
        }

        return view('chat::chat.index')->with($data);
    }

    public function ProjectChatIndex(Request $request)
    {
        $data['conversationType'] = 'projects';
        $conversationId = $request->get('conversationId');
        $data['conversationId'] = ! empty($conversationId) ? $conversationId : 0;

        $data['enableGroupSetting'] = false;// isGroupChatEnabled();
        $data['membersCanAddGroup'] = false;// canMemberAddGroup();
        $data['myContactIds'] = [];
        $data['blockUserIds'] = [];
        $data['blockedByMeUserIds'] = [];

        /** @var Setting $setting */
        $setting = Setting::where('key', 'notification_sound')->pluck('value', 'key')->toArray();
        if (isset($setting['notification_sound'])) {
            $data['notification_sound'] = app(Setting::class)->getNotificationSound($setting['notification_sound']);
        }

        return view('chat::chat.project-chats-index')->with($data);
    }
}
