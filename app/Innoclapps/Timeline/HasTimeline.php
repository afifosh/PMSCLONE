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

namespace App\Innoclapps\Timeline;

use App\Innoclapps\Models\PinnedTimelineSubject;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTimeline
{
    /**
     * Boot the HasTimeline trait
     *
     * @return void
     */
    protected static function bootHasTimeline()
    {
        static::deleting(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                $model->pinnedTimelineables->each->delete();
            }
        });
    }

    /**
     * Get the timeline subject key
     *
     * @return string
     */
    public static function getTimelineSubjectKey()
    {
        return strtolower(class_basename(get_called_class()));
    }

    /**
     * Get the subject pinned timelineables models
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pinnedTimelineables() : MorphMany
    {
        return $this->morphMany(PinnedTimelineSubject::class, 'subject');
    }
}
