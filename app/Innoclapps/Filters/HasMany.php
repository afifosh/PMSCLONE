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

namespace App\Innoclapps\Filters;

use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\QueryBuilder\Parser;

class HasMany extends OperandFilter
{
    /**
     * Apply the filter when custom query callback is provided
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed $value
    *  @param string    $condition
    *  @param array     $sqlOperator
     * @param stdClass $rule
     * @param \App\Innoclapps\QueryBuilder\Parser $parser
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder, $value, $condition, $sqlOperator, $rule, Parser $parser)
    {
        if ($parser->ruleCountsRelation($rule->operand->rule)) {
            return $parser->makeQueryWhenCountableRelation(
                $builder,
                $rule->operand->rule,
                $rule,
                $sqlOperator['operator'],
                $value,
                $condition,
                function ($builder) {
                    return $this->applyViewAuthorizedCriteriaIfNeeded($builder);
                }
            );
        }

        return $builder->has($this->field(), '>=', 1, $condition, function ($builder) use ($rule, $parser) {
            $builder = $this->applyViewAuthorizedCriteriaIfNeeded($builder);

            // Use AND for the subquery of the relation rules
            return $parser->makeQuery($builder, $rule, 'AND');
        });
    }

    /**
     * Apply view authorized criteria to the builder if the builder model
     * is associated with resources e.q. in has or whereHas
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyViewAuthorizedCriteriaIfNeeded($builder)
    {
        if ($resource = Innoclapps::resourceByModel($builder->getModel())) {
            if ($criteria = $resource->viewAuthorizedRecordsCriteria()) {
                // We will pass the repository fictional only
                $builder = (new $criteria)->apply($builder, $resource->repository());
            }
        }

        return $builder;
    }

    /**
     * Check whether the filter has custom callback
     *
     * @return boolean
     */
    public function hasCustomQuery() : bool
    {
        return true;
    }
}
