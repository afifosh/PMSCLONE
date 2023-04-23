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

namespace App\Repositories;

use App\Models\Admin;
use App\Support\TransferUserData;
use Illuminate\Http\UploadedFile;
use App\Support\PurgesOAuthAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Innoclapps\Facades\Innoclapps;
use Illuminate\Support\Facades\Storage;
use App\Innoclapps\Repository\AppRepository;
use App\Contracts\Repositories\TeamRepository;
use App\Contracts\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepositoryEloquent extends AppRepository implements UserRepository
{
    use PurgesOAuthAccount;

    /**
     * The userid to transfer the data on delete
     *
     * @var int|null
     */
    public $transferDataTo;

    /**
     * Searchable fields
     *
     * @var array
     */
    protected static $fieldSearchable = [
        'name'  => 'like',
        'email' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Admin::class;
    }

    /**
     * Save a new entity in repository
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        if (isset($data['super_admin']) && (bool) $data['super_admin'] === true) {
            $data['access_api'] = true;
        }

        $data['password'] = Hash::make($data['password']);

        $user = parent::create($data);

        if (isset($data['notifications'])) {
            Innoclapps::updateNotificationSettings($user, $data['notifications']);
        }

        $user->assignRole($data['roles'] ?? []);

        $teamRepository = app(TeamRepository::class);

        collect($data['teams'] ?? [])->each(function ($teamId) use ($user, $teamRepository) {
            try {
                $teamRepository->update(['members' => $user->getKey()], $teamId);
            } catch (ModelNotFoundException $e) {
                // In case the team is deleted before the invitation is accepted
            }
        });

        return $user;
    }

    /**
     * Create user via the installer
     *
     * @param array $attributes
     *
     * @return \App\Models\Admin
     */
    public function createViaInstall(array $attributes) : Admin
    {
        return Admin::unguarded(function () use ($attributes) {
            $attributes['super_admin'] = true;
            $attributes['access_api'] = true;
            $attributes['first_day_of_week'] = default_setting('first_day_of_week');
            $attributes['time_format'] = default_setting('time_format');
            $attributes['date_format'] = default_setting('date_format');
            $attributes['timezone'] = $attributes['timezone'];

            return parent::create($attributes);
        });
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $data
     * @param int $id
     *
     * @return mixed
     */
    public function update(array $data, $id)
    {
        if (isset($data['super_admin']) && (bool) $data['super_admin'] === true) {
            $data['access_api'] = true;
        }

        if (array_key_exists('password', $data)) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }
        }

        $user = parent::update($data, $id);

        if (isset($data['notifications'])) {
            Innoclapps::updateNotificationSettings($user, $data['notifications']);
        }

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Boot the repository
     *
     * @return void
     */
    public static function boot()
    {
        static::deleting(function ($model, $repository) {
            if ($model->id === Auth::id()) {
                /**
                 * User cannot delete own account
                 */
                abort(409, __('user.delete_own_account_warning'));
            } elseif ($repository->transferDataTo === $model->id) {
                /**
                 * User cannot transfer the data to the same user
                 */
                abort(409, __('user.delete_transfer_to_same_user_warning'));
            }

            /**
             * The data must be transfered because of foreign keys
             */
            (new TransferUserData($repository->transferDataTo ?? Auth::id(), $model->id))();

            /**
             * Detach all the teams the user belongs to
             */
            $model->teams()->detach();

            /**
             * Detach any activities the user is attending to
             */
            $model->guests()->delete();

            /**
             * Purge user non-shared filters
             *
             * Shared filters will be transfered
             */
            $model->filters()->where('is_shared', 0)->delete();

            // Purge user non shared mail templates
            // Share templates will be transferred
            $model->predefinedMailTemplates()->where('is_shared', 0)->delete();

            // Delete all Zapier hooks as this user is no longer applicable
            // for Zapier integration as it's deleted.
            $model->zapierHooks()->delete();

            // Remove user dashboards
            $model->dashboards()->delete();

            /**
            * Delete user personal email accounts
            */
            $model->personalEmailAccounts->each->delete();

            $model->oAuthAccounts->each(function ($account) {
                $this->purgeOAuthAccount($account);
            });

            // Remove the user connected oAuth calendar
            $model->calendar?->delete();

            /**
             * Delete notifications
             */
            $model->notifications()->delete();

            // Delete comments
            $model->comments->each->delete();

            if ($model->avatar) {
                $this->removeAvatarImage($model);
            }

            $model->load('visibilityDependents.group');
            $model->visibilityDependents->each(function ($model) {
                $model->group->teams()->detach();
                $model->group->users()->detach();
            });
        });
    }

    /**
     * Delete user by a given id
     *
     * @param mixed $id
     * @param int|null $transferDataTo
     *
     * @return boolean
     */
    public function delete($id, ?int $transferDataTo = null)
    {
        $this->transferDataTo = $transferDataTo;

        return tap(parent::delete($id), function () {
            $this->transferDataTo = null;
        });
    }

    /**
     * Find user by email address
     *
     * @param string $email
     *
     * @return \App\Models\Admin|null
     */
    public function findByEmail(string $email) : ?Admin
    {
        return $this->limit(1)->findByField('email', $email)->first();
    }

    /**
     * Store the given user avatar
     *
     * @param \App\Models\Admin $user
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return void
     */
    public function storeAvatar(Admin $user, UploadedFile $file) : Admin
    {
        $this->removeAvatarImage($user);

        return $this->update([
            'avatar' => $file->store('avatars', 'public'),
        ], $user->id);
    }

    /**
     * Delete user avatar
     *
     * @param \App\Models\Admin $user
     *
     * @return static
     */
    public function removeAvatarImage(Admin $user) : static
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $this;
    }

    /**
     * The relations that are required for the response
     *
     * @return array
     */
    protected function eagerLoad()
    {
        $this->withCount('unreadNotifications');

        return [
            'latestFifteenNotifications',
            'roles.permissions',
            'dashboards',
        ];
    }
}
