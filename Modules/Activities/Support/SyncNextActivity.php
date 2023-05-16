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

namespace Modules\Activities\Support;

use Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Activities\Concerns\HasActivities;
use Modules\Activities\Models\Activity;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Import\Import;
use Modules\Core\Resource\Resource;

class SyncNextActivity
{
    /**
     * Sync the resources next activity date
     */
    public function __invoke(): void
    {
        if (Import::isImportInProgress()) {
            // Avoid error: General error: 1205 Lock wait timeout exceeded; try restarting transaction
            return;
        }

        foreach (static::resourcesWithNextActivity() as $resource) {
            $this->forResource($resource);
        }
    }

    /**
     * Sync the next activity for the given resource
     */
    protected function forResource(Resource $resource): void
    {
        $resource::model()::unguarded(function () use ($resource) {
            $this->syncFinished($resource->newModel());
            $this->syncNextActivity($resource->newModel());
        });
    }

    /**
     * Get all of the resources with next activity
     */
    public static function resourcesWithNextActivity(): Collection
    {
        return Innoclapps::registeredResources()->filter(
            fn ($resource) => in_array(HasActivities::class, class_uses_recursive($resource::$model))
        );
    }

    /**
     * Sync the resource next activity
     */
    protected function syncNextActivity(Model $model): void
    {
        $records = $this->recordsWithIncompleteActivitiesAndInFuture($model);

        $recordsToUpdate = $records->map(function ($record) {
            return array_merge([
                $record->getKeyName() => $record->getKey(),
                'next_activity_id' => $record->activities->first()->getKey(),

            ], $record->usesTimestamps() ? [
                $record->getUpdatedAtColumn() => $record->updated_at->format($record->getDateFormat()),
            ] : []);
        })->all();

        $this->performBatchUpdate($recordsToUpdate, $model);
    }

    /**
     * Get record that are with incomplete activities and due date is in the future
     */
    protected function recordsWithIncompleteActivitiesAndInFuture(Model $model): Collection
    {
        $columns = array_merge(
            [$model->getKeyName()],
            $model->usesTimestamps() ? [$model->getUpdatedAtColumn()] : []
        );

        return $model->newQuery()->select($columns)
            ->whereHas(
                'activities',
                fn ($query) => $query->incompleteAndInFuture()->orderBy(Activity::dueDateQueryExpression())
            )
            ->with([
                'activities' => fn ($query) => $query->incompleteAndInFuture()
                    ->select(['id', 'due_date', 'due_time'])
                    ->orderBy(Activity::dueDateQueryExpression()),
            ])
            ->get();
    }

    /**
     * Sync the finished resources
     *
     * Update next activity date to null where the resources doesn't have any incomplete activities
     */
    protected function syncFinished(Model $model): void
    {
        $attributes = ['next_activity_id' => null];

        // Do not update the "updated_at" column, we will pass the actual colum name to use the current value
        if ($model->usesTimestamps()) {
            $attributes[$model->getUpdatedAtColumn()] = DB::raw('updated_at');
        }

        $model->newQuery()->whereDoesntHave('activities', function ($query) {
            return $query->incomplete()->where(
                Activity::dueDateQueryExpression(),
                '>=',
                now()
            );
        })->update($attributes);
    }

    /**
     * Perform batch next activity date update
     */
    protected function performBatchUpdate(array $records, Model $model): void
    {
        $modifiedRecords = Batch::update($model, $records, $model->getKeyName());

        if ($modifiedRecords) {
            // Clear the cache as the Batch updater is using direct connection
            // to the database in this case, the model event won't be triggered
            // Artisan::call('modelCache:clear', ['--model' => $model::class]);
        }
    }
}
