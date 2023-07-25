<?php

namespace Modules\Chat\Http\Controllers\API;

use Modules\Chat\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Modules\Chat\Models\Notification;
use Modules\Chat\Repositories\NotificationRepository;

/**
 * Class NotificationController
 */
class NotificationController extends AppBaseController
{
    /** @var NotificationRepository */
    private $notificationRepo;

    /**
     * Create a new controller instance.
     *
     * @param  NotificationRepository  $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepo = $notificationRepository;
    }

    /**
     * @param  Notification  $notification
     * @return JsonResponse
     */
    public function readNotification(Notification $notification)
    {
        $this->notificationRepo->readNotification($notification->id);

        return $this->sendResponse($notification, 'Message read successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function readAllNotification()
    {
        $messageSenderIds = $this->notificationRepo->readAllNotification();

        return $this->sendResponse(['sender_ids' => $messageSenderIds], 'Read all messages successfully.');
    }
}
