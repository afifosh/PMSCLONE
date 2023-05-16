<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Activities\Concerns;

use Illuminate\Support\Facades\Auth;
use Modules\Activities\Models\ActivityType;
use Modules\Activities\Services\ActivityService;
use Modules\Core\Date\Carbon;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Resource\Http\ResourceRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait CreatesFollowUpTask
{
    /**
     * Create follow up task
     *
     * @param  string  $taskDate
     * @param  string  $viaResource
     * @param  int  $viaResourceId
     * @return \Modules\Activities\Models\Activity
     */
    public function createFollowUpTask($taskDate, $viaResource, $viaResourceId, array $attributes = [])
    {
        $resource = Innoclapps::resourceByName($viaResource);
        $primaryModel = $resource->newModel()->find($viaResourceId);

        $dateInUTCTimezone = Carbon::asCurrentTimezone($taskDate)
            ->setHour(config('activities.defaults.hour'))
            ->setMinute(config('activities.defaults.minutes'))
            ->setSecond(0)
            ->inAppTimezone();

        $task = (new ActivityService())->create(array_merge([
            'activity_type_id' => ActivityType::findByFlag('task')->getKey(),
            'title' => __('activities::activity.follow_up_with_title', ['with' => $primaryModel->display_name]),
            'due_date' => $dueDate = $dateInUTCTimezone->format('Y-m-d'),
            'due_time' => $dateInUTCTimezone->format('H:i:s'),
            'end_date' => $dueDate,
            'reminder_minutes_before' => config('activities.defaults.reminder_minutes'),
            'user_id' => Auth::id(),
        ], $attributes));

        $task->{$resource->associateableName()}()->sync($viaResourceId);

        $task = $task->resource()->displayQuery()->find($task->id);

        try {
            resolve(ResourceRequest::class)->resource()
                ->jsonResource()::withActivity($task);
        } catch (NotFoundHttpException) {
            // When using the trait with non registered resource e.q. message
        }

        return $task;
    }

    /**
     * Check whether a follow up task should be created
     * It checks via the request data attributes
     */
    public function shouldCreateFollowUpTask(array $data): bool
    {
        return isset($data['task_date']) && ! empty($data['task_date']);
    }
}
