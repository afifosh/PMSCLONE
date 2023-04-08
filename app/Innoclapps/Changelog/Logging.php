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

namespace App\Innoclapps\Changelog;

use Closure;
use App\Innoclapps\Models\Changelog;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Facades\CauserResolver;

class Logging
{
    /**
     * The log trigger method
     */
    protected const LOGER_TRIGGER_METHOD = 'log';

    /**
     * Set the log causer
     *
     * @param \App\Models\Admin|null $causer
     *
     * @return void
     */
    public function setCauser($causer) : void
    {
        CauserResolver::setCauser($causer);
    }

    /**
     * Execute callback in disabled state
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public function disabled(Closure $callback) : void
    {
        $this->disable();

        try {
            $callback($this);
        } finally {
            $this->enable();
        }
    }

    /**
     * Disable changes logging
     *
     * @return static
     */
    public function disable() : static
    {
        $this->getLogger()->disableLogging();

        return $this;
    }

    /**
     * Enable changes logging
     *
     * @return static
     */
    public function enable() : static
    {
        $this->getLogger()->enableLogging();

        return $this;
    }

    /**
     * Get the logger
     *
     * @return \Spatie\Activitylog\ActivityLogger
     */
    public function getLogger() : ActivityLogger
    {
        return activity();
    }

    /**
     * Log changes on the given model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     *
     * @return \App\Innoclapps\Changelog\PendingLog
     */
    public function onModel($model, array $attributes = []) : PendingLog
    {
        return $this->on($model)
            ->withProperties($attributes)
            ->generic()
            ->useModelLog();
    }

    /**
     * Get the latest created log for the current request
     *
     * @return null\App\Innoclapps\Models\Changelog
     */
    public function getLatestCreatedLog() : ?Changelog
    {
        return Changelog::$latestSavedLog;
    }

    /**
     * Forward calls to PendingLog or log via the logger
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $logger = new PendingLog($this->getLogger(), self::LOGER_TRIGGER_METHOD);

        // Check direct logging without any arguments
        // e.q. ChangeLogger::log('Log Message');
        if ($name === self::LOGER_TRIGGER_METHOD) {
            return $logger->log($arguments[0] ?? '');
        }

        return $logger->{$name}(...$arguments);
    }
}
