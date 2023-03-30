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

namespace App\Innoclapps\Fields;

trait HasModelEvents
{
    /**
     * Handle the resource record "creating" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordCreating($model)
    {
        //
    }

    /**
     * Handle the resource record "created" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordCreated($model)
    {
        //
    }

    /**
     * Handle the resource record "updating" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordUpdating($model)
    {
        //
    }

    /**
     * Handle the resource record "updated" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordUpdated($model)
    {
        //
    }

    /**
     * Handle the resource record "deleting" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordDeleting($model)
    {
        //
    }

    /**
     * Handle the resource record "deleted" event
     *
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return void
     */
    public function recordDeleted($model)
    {
        //
    }
}
