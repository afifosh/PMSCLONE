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

use Modules\Core\Filters\Filter;
use Modules\Core\ProvidesBetweenArgumentsViaString;

class DueTodayActivities extends Filter
{
    use ProvidesBetweenArgumentsViaString;

    /**
     * Initialize DueTodayActivities class
     */
    public function __construct()
    {
        parent::__construct('due_today', __('activities::activity.filters.due_today'));

        $this->asStatic()->query(function ($builder, $value, $condition) {
            return $builder->where(function ($builder) {
                $builder->dueToday();
            }, null, null, $condition);
        });
    }
}
