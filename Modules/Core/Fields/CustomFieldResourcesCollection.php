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

namespace Modules\Core\Fields;

use Illuminate\Database\Eloquent\Collection;

class CustomFieldResourcesCollection extends Collection
{
    /**
     * Cached resource collection
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Query custom fields for resource
     *
     * @param  string  $resourceName
     * @return \Modules\Core\Fields\CustomFieldResourceCollection
     */
    public function forResource($resourceName)
    {
        if (array_key_exists($resourceName, $this->cache)) {
            return $this->cache[$resourceName];
        }

        return $this->cache[$resourceName] = new CustomFieldResourceCollection(
            $this->where('resource_name', $resourceName)
        );
    }
}
