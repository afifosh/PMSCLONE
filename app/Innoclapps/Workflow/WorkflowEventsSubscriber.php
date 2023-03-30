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

use App\Innoclapps\Contracts\Repositories\WorkflowRepository;

class WorkflowEventsSubscriber
{
    /**
     * Create new WorkflowEventsSubscriber instance.
     *
     * @param \App\Innoclapps\Contracts\Repositories\WorkflowRepository $repository
     */
    public function __construct(protected WorkflowRepository $repository)
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        foreach (Workflows::$eventOnlyListeners as $data) {
            $events->listen($data['event'], function ($event) use ($data) {
                $workflows = $this->repository->findWhere(['trigger_type' => $data['trigger']]);

                foreach ($workflows as $workflow) {
                    Workflows::process($workflow, ['event' => $event]);
                }
            });
        }
    }
}
