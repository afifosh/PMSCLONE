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

namespace Modules\Activities\Filters;

use Modules\Activities\Models\Activity;
use Modules\Core\Filters\DateTime;
use Modules\Core\QueryBuilder\Parser;

class ResourceNextActivityDate extends DateTime
{
    /**
     * Create new instance of ResourceNextActivityDate class
     */
    public function __construct()
    {
        parent::__construct('next_activity_date', __('activities::activity.next_activity_date'));

        $this->withNullOperators()
            ->withoutOperators('was')
            ->query(function ($builder, $value, $condition, $sqlOperator, $rule, Parser $parser) {
                return $builder->whereHas(
                    'nextActivity',
                    function ($query) use ($value, $parser, $rule, $condition, $sqlOperator) {
                        $rule->query->rule = Activity::dueDateQueryExpression();

                        return $parser->makeQueryWhenDate($query, $this, $rule, $sqlOperator['operator'], $value, $condition);
                    }
                );
            });
    }
}
