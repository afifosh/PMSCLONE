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

use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Contracts\Workflow\EventTrigger;
use App\Innoclapps\Contracts\Workflow\FieldChangeTrigger;
use App\Innoclapps\Contracts\Repositories\WorkflowRepository;

trait HasWorkflowTriggers
{
    /**
     * Register model triggers events
     *
     * @return void
     */
    protected static function bootHasWorkflowTriggers()
    {
        foreach (static::getModelEventTriggers() as $trigger) {
            static::{$trigger::event()}(function ($model) use ($trigger) {
                foreach (static::getTriggerWorkflows($trigger::identifier()) as $workflow) {
                    // We will queue the workflow to be executed in the middleware
                    // just before the response is sent to the browser
                    // this will allow any associations or data added to the model
                    // after the model event to be available to the workflow action
                    Workflows::addToQueue($workflow, [
                        'model'    => $model,
                        'resource' => Innoclapps::resourceByModel($model),
                    ]);
                }
            });
        }

        foreach (static::getFieldChangeEventTriggers() as $trigger) {
            static::updated(function ($model) use ($trigger) {
                foreach (static::getTriggerWorkflows($trigger::identifier()) as $workflow) {
                    if (static::hasWorkflowFieldChanged($workflow, $model, $trigger)) {
                        Workflows::process($workflow, [
                            'model'    => $model,
                            'resource' => Innoclapps::resourceByModel($model),
                        ]);
                    }
                }
            });
        }
    }

    /**
     * Check whether the model field has changed
     *
     * @param \App\Innoclapps\Models\Workflow $workflow
     * @param $this $model
     * @param \App\Innoclapps\Contracts\Workflow\FieldChangeTrigger $trigger
     *
     * @return boolean
     */
    protected static function hasWorkflowFieldChanged($workflow, $model, FieldChangeTrigger $trigger)
    {
        $value    = $model->{$trigger::field()};
        $original = $model->getOriginal($trigger::field());
        $expected = $workflow->data[$trigger::changeField()->attribute];

        if ($value == $original) {
            return false;
        }

        if ($model->isEnumCastable($trigger::field())) {
            return is_int($expected) ? $value->value === $expected : $value->name === $expected;
        }

        return $value == $expected;
    }

    /**
     * Get the triggers which are triggered on specific event
     *
     * @return \Illuminate\Support\Collection
     */
    protected static function getModelEventTriggers()
    {
        return Workflows::triggersByModel(static::class)->whereInstanceOf(EventTrigger::class);
    }

    /**
      * Get the triggers which are triggered on specific event
      *
      * @return \Illuminate\Support\Collection
      */
    protected static function getFieldChangeEventTriggers()
    {
        return Workflows::triggersByModel(static::class)->whereInstanceOf(FieldChangeTrigger::class);
    }

    /**
     * Get the trigger saved workflows
     *
     * @param string $trigger
     *
     * @return array
     */
    protected static function getTriggerWorkflows(string $trigger)
    {
        return once(function () use ($trigger) {
            return resolve(WorkflowRepository::class)->findWhere(
                [
                    'trigger_type' => $trigger,
                    'is_active'    => true,
                ]
            );
        });
    }
}
