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

namespace Modules\Activities\Services;

use Illuminate\Support\Arr;
use Modules\Activities\Jobs\CreateCalendarEvent;
use Modules\Activities\Jobs\UpdateCalendarEvent;
use Modules\Activities\Models\Activity;
use Modules\Activities\Models\ActivityType;
use Modules\Core\Contracts\Services\CreateService;
use Modules\Core\Contracts\Services\Service;
use Modules\Core\Contracts\Services\UpdateService;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Models\Model;
use Modules\Users\Mention\PendingMention;
use Modules\Users\Models\User;

class ActivityService implements Service, CreateService, UpdateService
{
    public function create(array $attributes): Activity
    {
        $model = new Activity();

        $mention = new PendingMention($attributes['note'] ?? '');

        if ($mention->hasMentions()) {
            $attributes['note'] = $mention->getUpdatedText();
        }

        $attributes['end_date'] ??= $attributes['due_date'];

        if (! array_key_exists('activity_type_id', $attributes)) {
            $attributes['activity_type_id'] = ActivityType::getDefaultType();
        }

        $model->fill($attributes);

        if (($attributes['is_completed'] ?? null) === true) {
            $model->completed_at = now();
        }

        $model->save();

        if ($guests = $attributes['guests'] ?? null) {
            $this->saveGuests($model, $guests);
        }

        $this->notifyMentionedUsers(
            $mention,
            $model,
            $attributes['via_resource'] ?? null,
            $attributes['via_resource_id'] ?? null
        );

        if ($model->user->canSyncToCalendar() && $model->typeCanBeSynchronizedToCalendar()) {
            CreateCalendarEvent::dispatch($model->user->calendar, $model);
        }

        return $model;
    }

    public function update(Model $activity, array $attributes): Activity
    {
        $mention = new PendingMention($attributes['note'] ?? '');

        if ($mention->hasMentions()) {
            $attributes['note'] = $mention->getUpdatedText();
        }

        $isCompleted = $activity->isCompleted;
        $originalUser = $activity->user;

        $markAsCompleted = Arr::pull($attributes, 'is_completed');
        $activity->fill($attributes);

        if (! $isCompleted && $markAsCompleted === true) {
            $activity->completed_at = now();
        } elseif ($isCompleted && $markAsCompleted === false) {
            $activity->completed_at = null;
        }

        $activity->save();

        // For user relation in case changed
        if ($activity->wasChanged('user_id')) {
            $activity->refresh();
        }

        if ($guests = $attributes['guests'] ?? null) {
            $this->saveGuests($activity, $guests);
        }

        $this->notifyMentionedUsers(
            $mention,
            $activity,
            $attributes['via_resource'] ?? null,
            $attributes['via_resource_id'] ?? null
        );

        if ($activity->user_id !== $originalUser->getKey()) {
            $activity->deleteFromCalendar($originalUser);

            if ($activity->user->canSyncToCalendar() && $activity->typeCanBeSynchronizedToCalendar()) {
                CreateCalendarEvent::dispatch($activity->user->calendar, $activity);
            }
        } elseif ($activity->isSynchronizedToCalendar($activity->user->calendar) &&
            $activity->user->canSyncToCalendar() &&
            $activity->typeCanBeSynchronizedToCalendar()) {
            UpdateCalendarEvent::dispatch($activity->user->calendar, $activity, $activity->latestSynchronization()->pivot->event_id);
        }

        return $activity;
    }

    protected function saveGuests(Activity $activity, array $guests)
    {
        (new ActivityGuestService())->save($activity, $this->parseGuestsForSave($guests));
    }

    /**
     * Notify the mentioned users for the given mention
     *
     * @param  \Modules\Activities\Models\Activity  $activity
     * @param  string|null  $viaResource
     * @param  int|null  $viaResourceId
     * @return void
     */
    protected function notifyMentionedUsers(PendingMention $mention, $activity, $viaResource = null, $viaResourceId = null)
    {
        $intermediate = $viaResource && $viaResourceId ?
            Innoclapps::resourceByName($viaResource)->newModel()->find($viaResourceId) :
            $activity;

        $mention->setUrl($intermediate->path)->withUrlQueryParameter([
            'section' => $viaResource && $viaResourceId ? $activity->resource()->name() : null,
            'resourceId' => $viaResource && $viaResourceId ? $activity->getKey() : null,
        ])->notify();
    }

    /**
     * Parse the given guests array for save
     */
    protected function parseGuestsForSave(array $guests): array
    {
        $parsed = [];

        foreach ($guests as $resourceName => $ids) {
            $parsed = array_merge(
                $parsed,
                Innoclapps::resourceByName($resourceName)->newModel()->findMany($ids)->all()
            );
        }

        return $parsed;
    }
}
