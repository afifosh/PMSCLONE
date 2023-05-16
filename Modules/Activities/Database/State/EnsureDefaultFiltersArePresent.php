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

namespace Modules\Activities\Database\State;

use Illuminate\Support\Facades\DB;
use Modules\Activities\Filters\DueThisWeekActivities;
use Modules\Activities\Filters\DueTodayActivities;
use Modules\Activities\Filters\OpenActivities;
use Modules\Activities\Filters\OverdueActivities;
use Modules\Core\Models\Filter;

class EnsureDefaultFiltersArePresent
{
    public function __invoke()
    {
        if (DB::table('filters')->where('flag', 'open-activities')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'activities',
                'name' => 'activities::activity.filters.open',
                'flag' => 'open-activities',
                'rules' => [
                    OpenActivities::make()->toArray(),
                ],
            ])->save();
        }

        if (DB::table('filters')->where('flag', 'due-today-activities')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'activities',
                'name' => 'activities::activity.filters.due_today',
                'flag' => 'due-today-activities',
                'rules' => [
                    DueTodayActivities::make()->toArray(),
                ],
            ])->save();
        }

        if (DB::table('filters')->where('flag', 'due-this-week-activities')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'activities',
                'name' => 'activities::activity.filters.due_this_week',
                'flag' => 'due-this-week-activities',
                'rules' => [
                    DueThisWeekActivities::make()->toArray(),
                ],
            ])->save();
        }

        if (DB::table('filters')->where('flag', 'overdue-activities')->count() === 0) {
            $this->newModelInstance([
                'identifier' => 'activities',
                'name' => 'activities::activity.overdue',
                'flag' => 'overdue-activities',
                'rules' => [
                    OverdueActivities::make()->setOperator('equal')->setValue('yes')->toArray(),
                ],
            ])->save();
        }
    }

    protected function newModelInstance($attributes)
    {
        return new Filter(array_merge([
            'is_shared' => true,
            'is_readonly' => true,
        ], $attributes));
    }
}
