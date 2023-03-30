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

namespace App\Innoclapps\Zapier;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Innoclapps\Resources\Resource;
use App\Innoclapps\Resources\Http\ResourceRequest;
use App\Innoclapps\Contracts\Repositories\ZapierHookRepository;

class Zapier
{
    /**
     * @var \App\Innoclapps\Contracts\Repositories\ZapierHookRepository
     */
    protected ZapierHookRepository $repository;

    /**
     * Qeued data for hook request
     *
     * @var array
     */
    protected static array $queue = [];

    /**
     * Supported actions
     *
     * @var array
     */
    protected static array $supportedActions = ['created', 'updated'];

    /**
     * Initialize new Zapier instance.
     */
    public function __construct()
    {
        $this->repository = resolve(ZapierHookRepository::class);
    }

    /**
     * Add records to for the given action to the hooks queue
     *
     * @param string $action
     * @param int|array $records
     * @param \App\Innoclapps\Resources\Resource $resource
     *
     * @return static
     */
    public function queue(string $action, array|int $records, Resource $resource) : static
    {
        $this->validateActionName($action);

        if (! isset(static::$queue[$resource->name()])) {
            static::$queue[$resource->name()] = [];
        }

        if (! isset(static::$queue[$resource->name()][$action])) {
            static::$queue[$resource->name()][$action] = [];
        }

        static::$queue[$resource->name()][$action] = array_merge(
            static::$queue[$resource->name()][$action],
            Arr::wrap($records)
        );

        static::$queue[$resource->name()]['_resource'] = $resource;

        return $this;
    }

    /**
     * Process the queued action models
     *
     * @return void
     */
    public function processQueue() : void
    {
        foreach (static::$queue as $resourceName => $actions) {
            $resource = Arr::pull($actions, '_resource');

            foreach ($actions as $actionName => $modelIds) {
                $modelIds = array_unique($modelIds);
                if (count($modelIds) > 0 && $this->resourceZaps($resource->name(), $actionName)->isNotEmpty()) {
                    $models = $resource->displayQuery()
                        ->findWhereIn(
                            $resource->repository()->getModel()->getKeyName(),
                            $modelIds
                        );

                    $this->dispatchZaps($actionName, $resource, $models);
                }
            }
        }

        static::$queue = [];
    }

    /**
     * Dispatch the given action Zaps for processing for the given resource
     *
     * @param string $actionName
     * @param \App\Innoclapps\Resources\Resource $resource
     * @param \Illuminate\Support\Collection $models
     *
     * @return void
     */
    protected function dispatchZaps(string $actionName, Resource $resource, $models)
    {
        // Temporarily change the header for Zapier as the JsonResource class
        // is checking whether the request is for Zapier
        // Not needed to be an instance of ResourceRequest but for consistency, use the ResourceRequest class
        $request           = app(ResourceRequest::class)->setResource($resource->name());
        $originalUserAgent = $request->headers->get('user-agent');
        $request->headers->set('user-agent', 'Zapier');

        foreach ($this->resourceZaps($resource->name(), $actionName) as $zap) {
            ProcessZapHookAction::dispatch($zap->hook, json_decode(
                json_encode(
                    $resource->createJsonResource(
                        $this->filterModelsForDispatch($models, $zap, $actionName),
                        true,
                        $request
                    )
                )
            ));
        }

        // Reset the user-agent header
        $request->headers->set('user-agent', $originalUserAgent);
    }

    /**
     * Filter the models for dispatch
     *
     * @param \Illuminate\Support\Collection $models
     * @param \App\Innoclapps\Models\ZapierHook $zap
     * @param string $actionName
     *
     * @return \Illuminate\Support\Collection
     */
    protected function filterModelsForDispatch($models, $zap, $actionName)
    {
        return $models->filter(function ($model) use ($zap, $actionName) {
            if (isset($zap->data['triggerWhen'])) {
                // Filter empty values
                $triggerWhen = array_filter($zap->data['triggerWhen']);

                // Get the attributes keys only
                $attributes = array_keys($triggerWhen);

                switch ($actionName) {
                    case 'updated':
                    // Attributes not updated, exclude the model
                    if (! $model->isDirty($attributes)) {
                        return false;
                    }

                    // The attributes are not equal like the selected
                    // In this case, we exclude the model from the payload
                    if ($model->only($attributes) != $triggerWhen) {
                        return false;
                    }

                    break;
                    case 'created':
                    // Model attributes does not match the given attributes
                    if ($model->only($attributes) != $triggerWhen) {
                        return false;
                    }

                    break;
                }
            }

            return $zap->user->can('view', $model);
        })->values();
    }

    /**
     * Validate the given action name
     *
     * @param string $name
     *
     * @throws \App\Innoclapps\Zapier\ActionNotSupportedException
     */
    public function validateActionName(string $name) : void
    {
        throw_unless(
            in_array($name, static::$supportedActions),
            new ActionNotSupportedException($name)
        );
    }

    /**
     * Get the supported actions
     *
     * @return array
     */
    public function supportedActions() : array
    {
        return static::$supportedActions;
    }

    /**
     * Get the given action Zaps for the given resource
     *
     * @param string $name
     * @param string $actionName
     *
     * @return \Illuminate\Support\Collection
     */
    protected function resourceZaps(string $name, string $actionName)
    {
        return $this->repository->getResourceActionZaps($name, $actionName);
    }
}
