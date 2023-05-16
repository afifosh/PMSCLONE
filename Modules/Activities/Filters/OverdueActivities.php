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

use Modules\Core\Filters\Radio;

class OverdueActivities extends Radio
{
    /**
     * Create new instance of OverdueActivities class
     */
    public function __construct()
    {
        parent::__construct('overdue', __('activities::activity.overdue'));

        $this->options(['yes' => __('core::app.yes'), 'no' => __('core::app.no')])
            ->query(function ($builder, $value, $condition) {
                return $builder->where(fn ($builder) => $builder->overdue($value === 'yes' ? '<=' : '>'), null, null, $condition);
            });
    }
}
