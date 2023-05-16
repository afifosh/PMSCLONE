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

namespace Modules\Activities\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Activities\Criteria\ViewAuthorizedActivitiesCriteria;
use Modules\Activities\Filters\DueThisWeekActivities;
use Modules\Activities\Filters\DueTodayActivities;
use Modules\Activities\Filters\OpenActivities;
use Modules\Activities\Filters\OverdueActivities;
use Modules\Activities\Http\Resources\ActivityResource;
use Modules\Activities\Models\ActivityType;
use Modules\Activities\Services\ActivityService;
use Modules\Comments\Contracts\HasComments;
use Modules\Comments\Contracts\PipesComments;
use Modules\Core\Actions\DeleteAction;
use Modules\Core\Contracts\Resources\Exportable;
use Modules\Core\Contracts\Resources\Importable;
use Modules\Core\Contracts\Resources\Mediable;
use Modules\Core\Contracts\Resources\Resourceful;
use Modules\Core\Contracts\Resources\Tableable;
use Modules\Core\Facades\Permissions;
use Modules\Core\Filters\DateTime as DateTimeFilter;
use Modules\Core\Filters\Radio as RadioFilter;
use Modules\Core\Filters\Select as SelectFilter;
use Modules\Core\Filters\Text as TextFilter;
use Modules\Core\Menu\MenuItem;
use Modules\Core\Models\Model;
use Modules\Core\Models\PinnedTimelineSubject;
use Modules\Core\QueryBuilder\Parser;
use Modules\Core\Resource\Http\ResourceRequest;
use Modules\Core\Resource\Resource;
use Modules\Core\Settings\SettingsMenuItem;
use Modules\Core\Table\Table;
use Modules\Users\Filters\UserFilter;

class Activity extends Resource implements Resourceful, Tableable, Mediable, Importable, Exportable, PipesComments, HasComments
{
    /**
     * The column the records should be default ordered by when retrieving
     */
    public static string $orderBy = 'title';

    /**
     * Indicates whether the resource is globally searchable
     */
    public static bool $globallySearchable = true;

    /**
     * The model the resource is related to
     */
    public static string $model = 'Modules\Activities\Models\Activity';

    /**
     * Get the resource service for CRUD operations.
     */
    public function service(): ActivityService
    {
        return new ActivityService();
    }

    /**
     * Get the menu items for the resource
     */
    public function menu(): array
    {
        return [
            MenuItem::make(static::label(), '/activities', 'Calendar')
                ->position(10)
                ->inQuickCreate()
                ->keyboardShortcutChar('A'),
        ];
    }

    /**
     * Provide the resource table class
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function table($query, Request $request): Table
    {
        if ($request->filled('activity_type_id') && is_numeric($request->activity_type_id)) {
            $query->where('activity_type_id', $request->integer('activity_type_id'));
        }

        return new ActivityTable($query, $request);
    }

    /**
     * Provides the resource available CRUD fields
     */
    public function fields(Request $request): array
    {
        return (new ActivityFields)($this, $request);
    }

    /**
     * Get the resource available filters
     */
    public function filters(ResourceRequest $request): array
    {
        return [
            TextFilter::make('title', __('activities::activity.title'))->withoutEmptyOperators(),
            UserFilter::make(__('activities::activity.owner'))->withoutNullOperators(),
            DateTimeFilter::make('owner_assigned_date', __('activities::activity.owner_assigned_date')),

            SelectFilter::make('activity_type_id', __('activities::activity.type.type'))
                ->valueKey('id')
                ->labelKey('name')
                ->options(function () {
                    return ActivityType::select(['id', 'name'])->get();
                }),

            RadioFilter::make('is_completed', __('activities::activity.is_completed'))->options([
                true => __('core::app.yes'),
                false => __('core::app.no'),
            ])->query(function ($builder, $value, $condition) {
                $method = $value ? 'completed' : 'incomplete';

                return $builder->{$method}($condition);
            }),

            with(DateTimeFilter::make('due_date', __('activities::activity.due_date')), function ($filter) {
                return $filter->query($this->dueAndEndDateFilterQueryCallback($filter));
            }),

            with(DateTimeFilter::make('end_date', __('activities::activity.end_date')), function ($filter) {
                return $filter->query($this->dueAndEndDateFilterQueryCallback($filter));
            }),

            DateTimeFilter::make('reminder_at', __('activities::activity.reminder')),
            UserFilter::make(__('core::app.created_by'), 'created_by')->withoutNullOperators()->canSeeWhen('view all activities'),
            OverdueActivities::make(),
            OpenActivities::make(),
            DueTodayActivities::make(),
            DueThisWeekActivities::make(),
            DateTimeFilter::make('updated_at', __('core::app.updated_at')),
            DateTimeFilter::make('created_at', __('core::app.created_at')),
        ];
    }

    /**
     * Get the query for the due and end date filter query callback
     *
     * @return callable
     */
    protected function dueAndEndDateFilterQueryCallback($filter)
    {
        return function ($builder, $value, $condition, $sqlOperator, $rule, Parser $parser) use ($filter) {
            $rule->query->rule = static::$model::dueDateQueryExpression();

            return $parser->makeQueryWhenDate($builder, $filter, $rule, $sqlOperator['operator'], $value, $condition);
        };
    }

    /**
     * Provide the criteria that should be used to query only records that the logged-in user is authorized to view
     */
    public function viewAuthorizedRecordsCriteria(): string
    {
        return ViewAuthorizedActivitiesCriteria::class;
    }

    /**
     * Get the json resource that should be used for json response
     */
    public function jsonResource(): string
    {
        return ActivityResource::class;
    }

    /**
     * Provides the resource available actions
     */
    public function actions(ResourceRequest $request): array
    {
        return [
            (new \Modules\Users\Actions\AssignOwnerAction)->onlyOnIndex(),
            (new \Modules\Activities\Actions\MarkActivityAsComplete)->withoutConfirmation(),
            (new \Modules\Activities\Actions\UpdateActivityType)->onlyOnIndex(),

            (new DeleteAction)->useName(__('core::app.delete')),

            (new DeleteAction)->isBulk()
                ->useName(__('core::app.delete'))
                ->authorizedToRunWhen(
                    fn ($request, $model) => $request->user()->can('bulk delete activities')
                ),
        ];
    }

    /**
     * Get the resource available cards
     */
    public function cards(): array
    {
        return [
            (new \Modules\Activities\Cards\MyActivities)->help(__('activities::activity.cards.my_activities_info')),
            (new \Modules\Activities\Cards\UpcomingUserActivities)->help(__('activities::activity.cards.upcoming_info')),
            (new \Modules\Activities\Cards\ActivitiesCreatedBySaleAgent)->canSeeWhen('view all activities')
                ->color('success')
                ->help(__('activities::activity.cards.created_by_agent_info')),
        ];
    }

    /**
     * Get the displayable singular label of the resource
     */
    public static function singularLabel(): string
    {
        return __('activities::activity.activity');
    }

    /**
     * Get the displayable label of the resource
     */
    public static function label(): string
    {
        return __('activities::activity.activities');
    }

    /**
     * Get the resource relationship name when it's associated
     */
    public function associateableName(): string
    {
        return 'activities';
    }

    /**
     * Get the countable relations when quering associated records
     */
    public function withCountWhenAssociated(): array
    {
        return ['comments'];
    }

    /**
     * Create query when the resource is associated for index.
     */
    public function associatedIndexQuery(Model $primary, bool $applyOrder = true): Builder
    {
        $query = parent::associatedIndexQuery($primary, $applyOrder);
        [$with, $withCount] = static::getEagerLoadable($this->fieldsForIndexQuery());

        // For associations keys to be included in the JSON resource
        return $query->with(
            array_merge(
                $this->availableAssociations()->map->associateableName()->all(),
                ['pinnedTimelineSubjects'],
                $with->all()
            )
        )->withCount($withCount->all())
            ->criteria($this->viewAuthorizedRecordsCriteria())
            ->withResponseRelations();
    }

    /**
     * Create the query when the resource is associated and the data is intended for the timeline
     */
    public function timelineQuery(Model $subject): Builder
    {
        return $this->associatedIndexQuery($subject, false)
            ->with('pinnedTimelineSubjects')
            ->withPinnedTimelineSubjects($subject)
            // Pinned are always first, then the non-completed sorted by due date asc
            ->orderBy((new PinnedTimelineSubject)->getQualifiedCreatedAtColumn(), 'desc')
            ->orderBy('completed_at', 'asc')
            ->orderBy(static::$model::dueDateQueryExpression(), 'asc');
    }

    /**
     * Register permissions for the resource
     */
    public function registerPermissions(): void
    {
        $this->registerCommonPermissions();

        Permissions::register(function ($manager) {
            $manager->group($this->name(), function ($manager) {
                $manager->view('view', [
                    'permissions' => [
                        'view attends and owned activities' => __('activities::activity.permissions.attends_and_owned'),
                    ],
                ]);

                $manager->view('export', [
                    'permissions' => [
                        'export activities' => __('core::app.export.export'),
                    ],
                ]);
            });
        });
    }

    /**
     * Register the settings menu items for the resource
     */
    public function settingsMenu(): array
    {
        return [
            SettingsMenuItem::make(__('activities::activity.activities'), '/settings/activities', 'Calendar')->order(21),
        ];
    }
}
