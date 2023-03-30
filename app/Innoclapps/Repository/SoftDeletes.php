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

namespace App\Innoclapps\Repository;

use App\Innoclapps\Criteria\WithTrashedCriteria;

trait SoftDeletes
{
    /**
     * Restore the given soft-deleted model id.
     *
     * @param $id
     *
     * @return boolean|array
     */
    public function restore($id)
    {
        $this->pushCriteria(WithTrashedCriteria::class);
        $models = $this->createModelsArray($id);
        $this->popCriteria(WithTrashedCriteria::class);

        $onlyOneModel = count($models) === 1;
        $result       = ! $onlyOneModel ? ['skipped' => [], 'restored' => []] : false;

        foreach ($models as $model) {
            $restored = $this->performRestoreOnModelWithEvents($model);

            if ($onlyOneModel) {
                $result = $restored;
            } elseif ($restored) {
                $result['restored'][] = $model->getKey();
            } else {
                $result['skipped'][] = $model->getKey();
            }
        }

        return $result;
    }

    /**
     * Force a hard delete on the given soft deleted model id.
     *
     * @param mixed $id
     *
     * @return boolean|array
     */
    public function forceDelete($id)
    {
        $this->pushCriteria(WithTrashedCriteria::class);
        $models = $this->createModelsArray($id);
        $this->popCriteria(WithTrashedCriteria::class);

        $onlyOneModel = count($models) === 1;
        $result       = ! $onlyOneModel ? ['skipped' => [], 'deleted' => []] : false;

        foreach ($models as $model) {
            $model->willForceDelete();

            $deleted = $this->performForceDeleteOnModelWithEvents($model);

            if ($onlyOneModel) {
                $result = $deleted;
            } elseif ($deleted) {
                $result['deleted'][] = $model->getKey();
            } else {
                $result['skipped'][] = $model->getKey();
            }
        }

        return $result;
    }

    /**
     * Perform delete on the given model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return boolean
     */
    protected function performDeleteOnModel($model)
    {
        return tap($model->delete(), function ($deleted) use ($model) {
            if ($deleted) {
                $this->fireModelEvent('trashed', $model, false);
            }
        });
    }

    /**
    * Perform restore on the given model with events
    *
    * @param \Illuminate\Database\Eloquent\Model $model
    *
    * @return boolean
    */
    protected function performForceDeleteOnModelWithEvents($model)
    {
        if ($this->fireModelEvent('deleting', $model) === false) {
            return false;
        }

        $result = $model->forceDelete();

        if ($result) {
            $this->fireModelEvent('deleted', $model, false);
            $this->fireModelEvent('forceDeleted', $model, false);
        }

        return $result;
    }

    /**
    * Perform restore on the given model with events
    *
    * @param \Illuminate\Database\Eloquent\Model $model
    *
    * @return boolean
    */
    protected function performRestoreOnModelWithEvents($model)
    {
        if ($this->fireModelEvent('restoring', $model) === false) {
            return false;
        }

        $result = $model->restore();

        if ($result) {
            $this->fireModelEvent('restored', $model, false);
        }

        return $result;
    }

    /**
     * Register a "softDeleted" model event callback with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function softDeleted($callback)
    {
        static::registerModelEvent('trashed', $callback);
    }

    /**
     * Register a "restoring" model event callback with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function restoring($callback)
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a "restored" model event callback with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function restored($callback)
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Register a "forceDeleted" model event callback with the dispatcher.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function forceDeleted($callback)
    {
        static::registerModelEvent('forceDeleted', $callback);
    }
}