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

namespace Modules\Core\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Contracts\Criteria\QueryCriteria;

class RelatedCriteria implements QueryCriteria
{
    /**
     * Apply the criteria for the given query.
     */
    public function apply(Builder $base): void
    {
        $base->where(function ($query) use ($base) {
            $resource = $base->getModel()->resource();

            foreach ($resource->availableAssociations() as $key => $resource) {
                if ($criteria = $resource->viewAuthorizedRecordsCriteria()) {
                    $whereCallback = function ($query) use ($criteria) {
                        (new $criteria)->apply($query);
                    };

                    $query->{$key === 0 ? 'whereHas' : 'orWhereHas'}($resource->associateableName(), $whereCallback);
                }
            }

            if (method_exists($base, 'user')) {
                $query->orWhere($base->user()->getForeignKeyName(), auth()->id());
            }
        });
    }
}
