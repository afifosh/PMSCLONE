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

namespace App\Innoclapps\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PinnedTimelineSubject extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
    * The "booted" method of the model.
    *
    * @return void
    */
    protected static function booted()
    {
        static::addGlobalScope('default_order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    /**
     * Get the subject of the pinned timeline
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the timelineable
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function timelineable() : MorphTo
    {
        return $this->morphTo();
    }
}
