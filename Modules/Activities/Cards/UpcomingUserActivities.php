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

namespace Modules\Activities\Cards;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Auth;
use Modules\Activities\Models\Activity;
use Modules\Core\Card\Card;
use Modules\Core\Card\TableAsyncCard;
use Modules\Core\ProvidesBetweenArgumentsViaString;

class UpcomingUserActivities extends TableAsyncCard
{
    use ProvidesBetweenArgumentsViaString;

    /**
     * The default selected range
     *
     * @var string
     */
    public string|int|null $defaultRange = 'this_month';

    /**
     * Provide the query that will be used to retrieve the items.
     */
    public function query(): Builder
    {
        return Activity::with('type')
            ->upcoming()
            ->incomplete()
            ->where('user_id', Auth::id())
            ->whereBetween(Activity::dueDateQueryExpression(), $this->getBetweenArguments(
                $this->getCurrentRange(request())
            ))
            ->orderBy(Activity::dueDateQueryExpression());
    }

    /**
     * Provide the table fields
     */
    public function fields(): array
    {
        return [
            ['key' => 'title', 'label' => __('activities::activity.title'), 'sortable' => true],
            ['key' => 'due_date', 'label' => __('activities::activity.due_date'), 'sortable' => true],
        ];
    }

    /**
     * Get the columns that should be selected in the query
     */
    protected function selectColumns(): array
    {
        return array_merge(
            parent::selectColumns(),
            // user_id is for authorization
            ['user_id']
        );
    }

    /**
     * Get the ranges available for the chart.
     */
    public function ranges(): array
    {
        return [
            'this_week' => __('core::dates.this_week'),
            'this_month' => __('core::dates.this_month'),
            'next_week' => __('core::dates.next_week'),
            'next_month' => __('core::dates.next_month'),
        ];
    }

    /**
     * Get the sort column
     */
    protected function getSortColumn(): Expression
    {
        return Activity::dueDateQueryExpression();
    }

    /**
     * The card name
     */
    public function name(): string
    {
        return __('activities::activity.cards.upcoming');
    }
}
