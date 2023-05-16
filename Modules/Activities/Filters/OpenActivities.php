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

class OpenActivities extends Filter
{
    /**
     * Initialize OpenActivities Class
     */
    public function __construct()
    {
        parent::__construct('open_activities', __('activities::activity.filters.open'));

        $this->asStatic()->query(function ($builder, $value, $condition) {
            return $builder->where(fn ($builder) => $builder->incomplete(), null, null, $condition);
        });
    }
}
