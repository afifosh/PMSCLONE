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

namespace App\Innoclapps\Workflow;

use Illuminate\Support\Collection;
use App\Innoclapps\Models\Workflow;
use App\Innoclapps\SubClassDiscovery;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Innoclapps\Contracts\Workflow\EventTrigger;
use App\Innoclapps\Contracts\Workflow\ModelTrigger;

class Workflows
{
    /**
     * The event only triggers
     *
     * @var array
     */
    public static array $eventOnlyListeners = [];

    /**
     * Registered triggers
     *
     * @var array
     */
    public static array $triggers = [];

    /**
     * Processed workflows actions
     *
     * @var array
     */
    public static array $processed = [];

    /**
     * Indicates whether the workflows are running
     *
     * @var boolean
     */
    protected static bool $workflowRunning = false;

    /**
     * Queued workflows
     *
     * @var array
     */
    protected static array $queue = [];

    /**
     * Process for running the given workflow
     *
     * NOTE: Actions are not executed during import
     *
     * @param \App\Innoclapps\Models\Workflow $workflow
     * @param array $data Additional data
     *
     * @return void
     */
    public static function process($workflow, array $data = []) : void
    {
        if (! static::workflowActionCanBeExecuted($workflow)) {
            return;
        }

        [$action, $trigger] = static::prepareActionForDispatch($workflow, $data);

        (function ($method) use ($action, $trigger, $workflow) {
            static::$processed[$action::class] = [
                'action'   => $action,
                'workflow' => $workflow,
                'trigger'  => $trigger,
            ];

            ProcessWorkflowAction::{$method}($action);
        })($action instanceof ShouldQueue ? 'dispatch' : 'dispatchSync');
    }

    /**
     * Process the queued workflows
     *
     * @return void
     */
    public static function processQueue() : void
    {
        foreach (static::$queue as $queue) {
            static::process($queue['workflow'], $queue['data']);
        }

        static::$queue = [];
    }

    /**
     * Add the workflow to the internal queue
     *
     * @param \App\Innoclapps\Models\Workflow $workflow
     * @param array $data
     *
     * @return void
     */
    public static function addToQueue(Workflow $workflow, array $data = []) : void
    {
        if (! static::workflowActionCanBeExecuted($workflow)) {
            return;
        }

        static::$queue[] = [
            'workflow' => $workflow,
            'data'     => $data,
        ];
    }

    /**
     * Check whether the workflow action can be executed
     *
     * @param \App\Innoclapps\Models\Workflow $workflow
     *
     * @return boolean
     */
    protected static function workflowActionCanBeExecuted($workflow) : bool
    {
        return static::newTriggerInstance($workflow->trigger_type)
            ->getAction($workflow->action_type)::allowedForExecution();
    }

    /**
     * Get the available triggers classes
     *
     * @return array
     */
    public static function availableTriggers() : array
    {
        return static::$triggers;
    }

    /**
     * Get the available triggers classes instance
     *
     * @return \Illuminate\Support\Collection
     */
    public static function triggersInstance() : Collection
    {
        return collect(static::availableTriggers())->map(fn ($trigger) => resolve($trigger));
    }

    /**
     * Create new trigger instance by a given trigger class
     *
     * @param string $class
     *
     * @return \App\Innoclapps\Workflow\Trigger
     */
    public static function newTriggerInstance(string $class) : Trigger
    {
        return collect(static::availableTriggers())
            ->filter(fn ($trigger) => $trigger === $class)
            ->map(fn ($trigger)    => resolve($trigger))
            ->first();
    }

    /**
     * Get triggers by a given model
     *
     * @param string|\Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Support\Collection
     */
    public static function triggersByModel($model) : Collection
    {
        return static::triggersInstance()
            ->whereInstanceOf(ModelTrigger::class)
            ->filter(fn ($trigger) => $trigger::model() === (! is_string($model) ? $model::class : $model));
    }

    /**
     * Register the given triggers.
     *
     * @param array $triggers
     *
     * @return void
     */
    public static function triggers(array $triggers) : void
    {
        static::$triggers = array_merge(static::$triggers, $triggers);
    }

    /**
     * Set whether the workflows are running
     *
     * @param boolean $value
     *
     * @return void
     */
    public static function workflowRunning(bool $value = true) : void
    {
        static::$workflowRunning = $value;
    }

    /**
     * Check whether the workflows are running
     *
     * @return boolean
     */
    public static function isWorkflowRunning() : bool
    {
        return static::$workflowRunning;
    }

    /**
     * Set the custom discovery path for the workflows triggers
     *
     * @param string $directory
     *
     * @return void
     */
    public static function triggersIn(string $directory)
    {
        $triggers = (new SubClassDiscovery(Trigger::class))->in($directory)->find();

        static::triggers(collect($triggers)->sort()->all());
    }

    /**
     * Register laravel event based triggers
     *
     * @return void
     */
    public static function registerEventOnlyTriggersListeners() : void
    {
        static::$eventOnlyListeners = collect(static::availableTriggers())
            ->filter(function ($trigger) {
                return is_a($trigger, EventTrigger::class, true) &&
                ! is_a($trigger, ModelTrigger::class, true);
            })->map(function ($trigger) {
                return [
                    'trigger' => $trigger,
                    'event'   => $trigger::event(),
                ];
            })->values()->all();
    }

    /**
     * Prepare the given workflow and data for execution
     *
     * @param \App\Innoclapps\Models\Workflow $workflow
     * @param array $data
     *
     * @return array
     */
    protected static function prepareActionForDispatch($workflow, $data)
    {
        // We will merge the workflow data, the provided custom data and the
        // actual workflow as data to be available in the action that will be executed for the workflow
        $actionData = array_merge(
            $workflow->data,
            $data,
            ['workflow' => $workflow]
        );

        $trigger = static::newTriggerInstance($workflow->trigger_type);

        return [$trigger->getAction($workflow->action_type)->setData($actionData), $trigger];
    }
}
