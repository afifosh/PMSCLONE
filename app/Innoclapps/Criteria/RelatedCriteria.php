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

namespace App\Innoclapps\Criteria;

use Illuminate\Database\Eloquent\Builder;
use App\Innoclapps\Contracts\Repository\CriteriaInterface;
use App\Innoclapps\Contracts\Repository\RepositoryInterface;

class RelatedCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param \App\Innoclapps\Contracts\Repository\RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where(function ($query) use ($repository, $model) {
            $resource = ($model instanceof Builder ? $model->getModel() : $model)::resource();

            foreach ($resource->availableAssociations() as $key => $resource) {
                if ($criteria = $resource->viewAuthorizedRecordsCriteria()) {
                    $whereCallback = function ($query) use ($repository, $criteria) {
                        return resolve($criteria)->apply($query, $repository);
                    };

                    $query->{$key === 0 ? 'whereHas' : 'orWhereHas'}($resource->associateableName(), $whereCallback);
                }
            }

            if (method_exists($model, 'user')) {
                $query->orWhere($model->user()->getForeignKeyName(), auth()->id());
            }

            return $query;
        });
    }
}
