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

namespace App\Innoclapps\Filters;

use App\Innoclapps\Models\Filter;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Criteria\UserFiltersCriteria;
use App\Innoclapps\Contracts\Repositories\FilterRepository;

class FilterRepositoryEloquent extends AppRepository implements FilterRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return Filter::class;
    }

    /**
     * Get available filters for users, including shared
     *
     * @param string $identifier
     * @param string|integer $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($identifier, $userId)
    {
        return tap($this->with(['defaults' => function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        }])->pushCriteria(new UserFiltersCriteria($identifier, $userId))->get(), function () {
            $this->popCriteria(UserFiltersCriteria::class);
        });
    }

    /**
      * Get the given user default filter
      *
      * @param string $identifier
      * @param string $view
      * @param int    $userId
      *
      * @return \App\Innoclapps\Models\Filter|null
      */
    public function findDefaultForUser($identifier, $view, $userId)
    {
        return tap($this->whereHas('defaults', function ($query) use ($view, $userId) {
            return $query->where('view', $view)->where('user_id', $userId);
        })->pushCriteria(new UserFiltersCriteria($identifier, $userId))->first(), function () {
            $this->popCriteria(UserFiltersCriteria::class);
        });
    }

    /**
     * Find filter by given flag
     *
     * @param string $flag
     *
     * @return \App\Innoclapps\Models\Filter|null
     */
    public function findByFlag(string $flag) : ?Filter
    {
        return $this->limit(1)->findByField('flag', $flag)->first();
    }

    /**
     * Mark filter as default for the given user
     *
     * @param integer $id
     * @param string|array $views
     * @param integer $userId
     *
     * @return \App\Innoclapps\Models\Filter
     */
    public function markAsDefault(int $id, string|array $views, int $userId)
    {
        $filter = $this->find($id);

        foreach ((array) $views as $view) {
            // We will check if there is current default filter for the view and the user
            // If yes, we will remove this default filter to leave space for the new one
            if ($currentDefault = $this->findDefaultForUser($filter->identifier, $view, $userId)) {
                $this->unMarkAsDefault($currentDefault->id, $view, $userId);
            }

            $filter->defaults()->create([
                'user_id' => $userId,
                'view'    => $view,
            ]);
        }

        return $filter->loadMissing('defaults');
    }

    /**
     * Unmark filter as default for the given user
     *
     * @param integer $id
     * @param string|array $views
     * @param int $userId
     *
     * @return \App\Innoclapps\Models\Filter
     */
    public function unMarkAsDefault(int $id, string|array $views, int $userId)
    {
        $filter = $this->find($id);

        foreach ((array) $views as $view) {
            $filter->defaults()
                ->where('view', $view)
                ->where('user_id', $userId)
                ->delete();
        }

        return $filter->loadMissing('defaults');
    }
}
