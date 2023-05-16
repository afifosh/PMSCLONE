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

namespace Modules\Core\Tests\Concerns;

use Illuminate\Support\Facades\Request;
use Modules\Core\Criteria\FilterRulesCriteria;
use Tests\Fixtures\Event;

trait TestsFilters
{
    /**
     * Perform filers search
     *
     * @param  Criteria  $criteria
     * @return \Illuminate\Support\Collection
     */
    protected function perform($attribute, $operand, $value = null)
    {
        $filter = app(static::$filter, ['field' => $attribute]);

        $rule = $this->payload(
            $attribute,
            $value,
            $filter->type(),
            $operand
        );

        return (new Event())
            ->newQuery()
            ->criteria(
                new FilterRulesCriteria($rule, collect([$filter]), Request::instance())
            )->get();
    }

    /**
     * Get filter payload
     *
     * @param  string  $field
     * @param  mixed  $value
     * @param  string  $type
     * @param  string  $operator
     * @return array
     */
    protected function payload($field, $value, $type, $operator)
    {
        $rule = [
            'type' => 'rule',
            'query' => [
                'type' => $type,
                'rule' => $field,
                'operator' => $operator,
                'value' => $value,
            ],
        ];

        return [
            'condition' => 'and',
            'children' => [$rule],
        ];
    }
}
