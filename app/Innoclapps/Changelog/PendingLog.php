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

use App\Innoclapps\Facades\Innoclapps;
use Spatie\Activitylog\ActivityLogger;
use App\Innoclapps\Facades\ChangeLogger;
use Spatie\Activitylog\ActivityLogStatus;
use Spatie\Activitylog\Contracts\Activity;

class PendingLog
{
    /**
     * The generic log identifier
     */
    protected const GENERIC_IDENTIFIER = 'generic';

    /**
     * Indicates that we should perform a log even when disabled
     *
     * @var boolean
     */
    protected bool $forceLogging = false;

    /**
     * Additional attributes/columns taps for the model
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Indicates that the log should be casted as system logs
     *
     * @var boolean
     */
    protected static bool $asSystem = false;

    /**
     * Initialize PendingLog
     *
     * @param \Spatie\Activitylog\ActivityLogger $logger
     * @param string $logTriggerMethod
     */
    public function __construct(protected ActivityLogger $logger, protected string $logTriggerMethod)
    {
    }

    /**
     * Force to log even if the logger is disabled
     *
     * @return static
     */
    public function forceLogging() : static
    {
        $this->forceLogging = true;

        return $this;
    }

    /**
     * Set log causer name
     *
     * @param string $name
     *
     * @return static
     */
    public function causerName(string $name) : static
    {
        $this->withAttributes(['causer_name' => $name]);

        return $this;
    }

    /**
     * Set log identifier attribute
     *
     * @param string $identifier
     *
     * @return static
     */
    public function identifier(string $identifier) : static
    {
        $this->withAttributes(['identifier' => $identifier]);

        return $this;
    }

    /**
     * Use the model non-clearable log name
     *
     * @return static
     */
    public function useModelLog() : static
    {
        $this->withAttributes(['log_name' => ChangeLogger::MODEL_LOG_NAME]);

        return $this;
    }

    /**
     * Indicates that the log is generic log identifier
     *
     * @return static
     */
    public function generic() : static
    {
        return $this->identifier(self::GENERIC_IDENTIFIER);
    }

    /**
     * Indicates that the logs will be casted as system logs
     *
     * Is static because system logs can be used for more logs
     * e.q. in foreach loop logs when the foreach loop will generate
     * multiple logs and all of them should be as system
     *
     * @see _call
     *
     * @return static
     */
    public function asSystem(bool $bool = true) : static
    {
        static::$asSystem = $bool;

        return $this;
    }

    /**
     * Add custom attributes to the changelog
     *
     * @param array $attributes
     *
     * @return static
     */
    public function withAttributes(array $attributes) : static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Trigger the log method
     *
     * @param mixed $arguments
     *
     * @return mixed
     */
    protected function triggerLogMethod($arguments)
    {
        return $this->logger->tap(function (Activity $activity) {
            foreach ($this->attributes as $attribute => $value) {
                $activity->{$attribute} = $value;
            }

            if (static::$asSystem) {
                $this->loggingAsSystem($activity);
            }
        })->{$this->logTriggerMethod}($arguments[0] ?? '');
    }

    /**
     * Modifies the Activity to as system
     *
     * @param \Spatie\Activitylog\Contracts\Activity $activity
     *
     * @return void
     */
    protected function loggingAsSystem(Activity $activity) : void
    {
        $activity->log_name    = strtolower(Innoclapps::systemName());
        $activity->causer_name = Innoclapps::systemName();
        $this->logger->byAnonymous();
    }

    /**
     * Call a method from the logger
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        // If finally user call ->log() try to merge the custom attributes
        // and call the log method

        if ($method === $this->logTriggerMethod) {
            if ($this->forceLogging && app(ActivityLogStatus::class)->disabled()) {
                ChangeLogger::enable();

                return tap($this->triggerLogMethod($arguments), function () {
                    ChangeLogger::disable();
                });
            }

            return $this->triggerLogMethod($arguments);
        }

        $this->logger->{$method}(...$arguments);

        return $this;
    }
}
