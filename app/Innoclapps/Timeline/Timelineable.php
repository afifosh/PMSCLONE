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

use Illuminate\Support\Str;

trait Timelineable
{
    /**
     * Boot the HasComments trait
     *
     * @return void
     */
    protected static function bootTimelineable()
    {
        static::deleting(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                $model->pinnedTimelineSubjects()->delete();
            }
        });
    }

    /**
     * Get the timeline pinnable subjects
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pinnedTimelineSubjects()
    {
        return $this->morphMany(\App\Innoclapps\Models\PinnedTimelineSubject::class, 'timelineable');
    }

    /**
     * Get the timeline pin
     *
     * @param string $subjectType
     * @param int $subjectId
     *
     * @return \App\Innoclapps\Models\PinnedTimelineSubject
     */
    public function getPinnedSubject($subjectType, $subjectId)
    {
        return $this->pinnedTimelineSubjects->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)->first();
    }

    /**
     * Get the timeline identifier
     *
     * @return string
     */
    public static function timelineKey()
    {
        return strtolower(class_basename(get_called_class()));
    }

    /**
     * Get the relation name when the model is used as timelineable
     *
     * @return string
     */
    public function getTimelineRelation()
    {
        return Str::plural(strtolower(class_basename(get_called_class())));
    }

    /**
     * Get the timeline component for front-end
     *
     * @return string
     */
    public function getTimelineComponent()
    {
        return strtolower(class_basename(get_called_class()));
    }
}
