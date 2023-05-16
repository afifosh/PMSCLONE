<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core;

use Illuminate\Support\Collection;
use Modules\Core\Resource\Http\ResourceRequest;

trait ResolvesActions
{
    /**
     * Get the available actions for the resource
     */
    public function resolveActions(ResourceRequest $request): Collection
    {
        $actions = $this->actions($request);

        $collection = is_array($actions) ? new Collection($actions) : $actions;

        return $collection->filter->authorizedToSee()->values();
    }

    /**
     * @codeCoverageIgnore
     *
     * Get the defined resource actions
     */
    public function actions(ResourceRequest $request): array|Collection
    {
        return [];
    }
}
