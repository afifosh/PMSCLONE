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

namespace Modules\Activities\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Activities\Models\Activity;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Users\Models\User;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any activities.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the activity.
     */
    public function view(User $user, Activity $activity): bool
    {
        if ($user->can('view all activities')) {
            return true;
        }

        if ($user->can('view attends and owned activities')) {
            return (int) $user->id === (int) $activity->user_id ||
                $activity->hasGuest($user) ||
                ($user->can('view team activities') && $user->managesAnyTeamsOf($activity->user_id));
        }

        if ((int) $user->id === (int) $activity->user_id) {
            return true;
        }

        if ($user->can('view team activities')) {
            return $user->managesAnyTeamsOf($activity->user_id);
        }

        return false;
    }

    /**
     * Determine if the given user can create activities.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the activity.
     */
    public function update(User $user, Activity $activity): bool
    {
        if ($user->can('edit all activities')) {
            return true;
        }

        if ($user->can('edit own activities')) {
            return (int) $user->id === (int) $activity->user_id;
        }

        if ($user->can('edit team activities')) {
            return $user->managesAnyTeamsOf($activity->user_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the activity.
     */
    public function delete(User $user, Activity $activity): bool
    {
        if ($user->can('delete any activity')) {
            return true;
        }

        if ($user->can('delete own activities')) {
            return (int) $user->id === (int) $activity->user_id;
        }

        if ($user->can('delete team activities')) {
            return $user->managesAnyTeamsOf($activity->user_id);
        }

        return false;
    }

    /**
     * Determine whether the user can export activities.
     */
    public function export(User $user): bool
    {
        return $user->can('export activities');
    }

    /**
     * Determine whether the user can mark the activity as complete|incomplete.
     */
    public function changeState(User $user, Activity $activity): bool
    {
        if ($this->update($user, $activity)) {
            return true;
        }

        return (int) $activity->user_id === (int) $user->id;
    }

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        $request = app(ResourceRequest::class);

        if ($ability === 'view' && $request->viaResource()) {
            return $request->findResource($request->get('via_resource'))
                ->newModel()
                ->find($request->get('via_resource_id'))
                ->activities
                ->contains(
                    $request->route()->parameter('resourceId')
                );
        }

        return null;
    }
}
