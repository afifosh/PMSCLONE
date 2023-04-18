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

namespace App\Innoclapps;

use Illuminate\Support\Collection;
use App\Innoclapps\Resources\Http\ResourceRequest;

trait ResolvesFilters
{
    /**
     *  Get the available filters for the user
     */
    public function resolveFilters(ResourceRequest $request) : Collection
    {
        $filters = $this->filters($request);

        $collection = is_array($filters) ? new Collection($filters) : $filters;

        return $collection->filter->authorizedToSee()->values();
    }

    /**
     * @codeCoverageIgnore
     *
     * Get the defined filters
     */
    public function filters(ResourceRequest $request) : array|Collection
    {
        return [];
    }
}
