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

use Illuminate\Support\Str;

class Number extends Filter implements CountableRelation
{
    /**
     * The relation that the count is performed on
     *
     * @var string|null
     */
    public $countableRelation;

    /**
     * Indicates that the filter will count the val ues
     *
     * @param string|null $relationName
     *
     * @return \App\Innoclapps\Filters\Filter
     */
    public function countableRelation($relationName = null)
    {
        $this->countableRelation = $relationName ?? lcfirst(Str::studly($this->field()));
        $operators               = $this->getOperators();

        // between and not_between are not supported at this time.
        unset($operators[array_search('between', $operators)], $operators[array_search('not_between', $operators)]);

        $this->operators($operators);

        return $this;
    }

    /**
     * Get the countable relation name
     *
     * @return string|null
     */
    public function getCountableRelation()
    {
        return $this->countableRelation;
    }

    /**
     * Defines a filter type
     *
     * @return string
     */
    public function type() : string
    {
        return 'number';
    }
}
