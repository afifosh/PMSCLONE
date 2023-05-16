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

namespace Modules\Core\Resource\Http;

use Illuminate\Database\Eloquent\Builder;

class TrashedResourcefulRequest extends ResourcefulRequest
{
    /**
     * Get new query for the current resource.
     */
    public function newQuery(): Builder
    {
        return $this->resource()->newQuery()->onlyTrashed();
    }
}
