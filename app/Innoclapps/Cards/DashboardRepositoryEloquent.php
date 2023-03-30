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

namespace App\Innoclapps\Cards;

use App\Models\User;
use App\Innoclapps\Models\Dashboard;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Contracts\Repositories\DashboardRepository;

class DashboardRepositoryEloquent extends AppRepository implements DashboardRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Dashboard::class;
    }

    /**
     * Get dashboards for user
     *
     * @param int|\App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser(int|User $user)
    {
        $id = is_int($user) ? $user : $user->id;

        return $this->orderBy('name')->findWhere(['user_id' => $id]);
    }

    /**
     * Create dashboard for user
     *
     * @param array $data
     * @param int|\App\Models\User $user
     *
     * @return \App\Innoclapps\Models\Dashboard
     */
    public function createForUser(array $data, int|User $user) : Dashboard
    {
        $data = array_merge($data, [
            'user_id' => is_int($user) ? $user : $user->id,
        ]);

        $data['is_default'] ??= false;
        $data['cards'] ??= Dashboard::defaultCards(
            Innoclapps::getUserRepository()->find($data['user_id'])
        );

        return tap($this->create($data), function ($dashboard) {
            if ($dashboard->is_default === true) {
                $this->massUpdate(['is_default' => false], [['id', '!=', $dashboard->id]]);
            }
        });
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $data
     * @param int $id
     *
     * @return \App\Innoclapps\Models\Dashboard
     */
    public function update(array $data, $id)
    {
        $dashboard = parent::update($data, $id);

        if ($dashboard->wasChanged('is_default') && $dashboard->is_default === true) {
            $this->massUpdate(['is_default' => false], [['id', '!=', $dashboard->id]]);
        }

        return $dashboard;
    }

    /**
     * Perform model update operation
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     *
     * @return void
     */
    protected function performUpdate($model, $attributes)
    {
        // Don't allow unmarking dashboard as default when there is only one dashboard
        if ($model->is_default === false && $this->hasOnlyOneDashboard($model->user_id)) {
            $model->fill(['is_default' => true]);
        }

        parent::performUpdate($model, $attributes);
    }

    /**
     * Check whether the given user has only one dashboard
     *
     * @param int|\App\Models\User $user
     *
     * @return boolean
     */
    public function hasOnlyOneDashboard(int|User $user) : bool
    {
        return $this->forUser($user)->count() === 1;
    }

    /**
     * Get the default dashboard for the given user
     *
     * @param int|\App\Models\User $user
     *
     * @return \App\Innoclapps\Models\Dashboard
     */
    public function getDefault(int|User $user) : Dashboard
    {
        $id = is_int($user) ? $user : $user->id;

        return $this->limit(1)->findWhere(['user_id' => $id, 'is_default' => true])->first();
    }

    /**
     * Create default dashboard for the given user
     *
     * @param int|\App\Models\User $user
     *
     * @return \App\Innoclapps\Models\Dashboard
     */
    public function createDefault(int|User $user) : Dashboard
    {
        return $this->createForUser([
            'name'       => 'Application Dashboard',
            'is_default' => true,
        ], $user);
    }
}
