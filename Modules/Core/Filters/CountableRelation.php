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

namespace Modules\Core\Filters;

interface CountableRelation
{
    /**
     * Indicates that the filter will count the values of the relation
     *
     * @param  string|null  $relationName
     * @return \Modules\Core\Filters\Filter
     */
    public function countableRelation($relationName = null);

    /**
     * Get the countable relation name
     *
     * @return string|null
     */
    public function getCountableRelation();
}
