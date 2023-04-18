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

namespace App\Innoclapps\Resources\Http;

use App\Innoclapps\Criteria\OnlyTrashedCriteria;

class TrashedResourcefulRequest extends ResourcefulRequest
{
    /**
     * Get the resource record for the current request
     *
     * @return int
     */
    public function record()
    {
        if (! $this->record) {
            $this->record = $this->resource()
                ->repository()
                ->pushCriteria(OnlyTrashedCriteria::class)
                ->find($this->resourceId());
        }

        return $this->record;
    }
}
