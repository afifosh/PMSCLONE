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

use App\Innoclapps\Timeline\Timelineable;
use Spatie\Activitylog\Models\Activity as SpatieActivityLog;

class Changelog extends SpatieActivityLog
{
    use Timelineable;

    /**
     * Latests saved activity
     *
     * @var null|\App\Innoclapps\Models\Changelog
     */
    public static $latestSavedLog;

    /**
     * Causer names cache, when importing a lot data helps making hundreds of queries
     *
     * @var array
     */
    protected static $causerNames = [];

    /**
     * Boot the model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        /**
         * Automatically set the causer name if the logged in
         * user is set and no causer name provided
         */
        static::creating(function ($model) {
            if (! $model->causer_name) {
                $model->causer_name = static::causerName($model);
            }
        });

        static::created(function ($model) {
            static::$latestSavedLog = $model;
        });
    }

    /**
     * Get the causer name for the given model
     *
     * @param \App\Innoclapps\Models\Changelog $model
     *
     * @return string|null
     */
    protected static function causerName($model)
    {
        if ($model->causer_id) {
            if (isset(static::$causerNames[$model->causer_id])) {
                return static::$causerNames[$model->causer_id];
            }

            return static::$causerNames[$model->causer_id] = $model->causer?->name;
        }

        return auth()->user()?->name;
    }

    /**
     * Get the relation name when the model is used as activity
     *
     * @return string
     */
    public function getTimelineRelation()
    {
        return 'changelog';
    }

    /**
     * Get the activity front-end component
     *
     * @return string
     */
    public function getTimelineComponent()
    {
        return $this->identifier;
    }
}
