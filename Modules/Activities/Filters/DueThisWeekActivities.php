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
use Modules\Core\Filters\Filter;
use Modules\Core\ProvidesBetweenArgumentsViaString;

class DueThisWeekActivities extends Filter
{
    use ProvidesBetweenArgumentsViaString;

    /**
     * Initialize DueThisWeekActivities class
     */
    public function __construct()
    {
        parent::__construct('due_this_week', __('activities::activity.filters.due_this_week'));

        $this->asStatic()->query(function ($builder, $value, $condition) {
            return $builder->where(function ($builder) {
                return $builder->whereBetween(
                    Activity::dueDateQueryExpression(),
                    $this->getBetweenArguments('this_week')
                )->incomplete();
            }, null, null, $condition);
        });
    }
}
