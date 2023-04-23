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

class SearchByFirstNameAndLastNameCriteria implements CriteriaInterface
{
    /**
     * Initialze new class
     *
     * @param string|null $relation
     */
    public function __construct(protected $relation = null)
    {
    }

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
        $search = request('q');

        if ($raw = ($model instanceof Builder ? $model->getModel() : $model)->nameQueryExpression()) {
            if (! $this->relation) {
                return $model->orWhere(function ($query) use ($search, $raw) {
                    $query->where($raw, 'LIKE', '%' . $search . '%');
                });
            }

            return $model->orWhere($this->relation, function ($query) use ($search, $raw) {
                $query->where($raw, 'LIKE', '%' . $search . '%');
            });
        }

        return $model;
    }
}
