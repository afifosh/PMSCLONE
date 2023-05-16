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

namespace Modules\Core\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Contracts\Countable;
use Modules\Core\Criteria\FilterRulesCriteria;
use Modules\Core\Criteria\TableRequestCriteria;
use Modules\Core\ProvidesModelAuthorizations;
use Modules\Core\ResolvesActions;
use Modules\Core\ResolvesFilters;
use Modules\Core\Resource\Http\ResourceRequest;

class Table
{
    use ParsesResponse,
        ResolvesFilters,
        ResolvesActions,
        HandlesRelations,
        ProvidesModelAuthorizations;

    /**
     * Additional relations to eager load on every query.
     */
    protected array $with = [];

    /**
     * Additional countable relations to eager load on every query.
     */
    protected array $withCount = [];

    /**
     * Custom table filters
     */
    protected Collection|array $filters = [];

    /**
     * Custom table actions
     */
    protected Collection|array $actions = [];

    /**
     * Table identifier
     */
    protected string $identifier;

    /**
     * Additional request query string for the table request
     */
    public array $requestQueryString = [];

    /**
     * Table order
     */
    public array $order = [];

    /**
     * Table default per page value
     */
    public int $perPage = 25;

    /**
     * Whether the table columns can be customized.
     * You must ensure all columns has unique ID's before setting this properoty to true
     */
    public bool $customizeable = false;

    /**
     * Whether the table sorting options can be changed.
     * Only works if $customizeable is set to true
     */
    public bool $allowDefaultSortChange = true;

    /**
     * Whether the table has actions column
     */
    public bool $withActionsColumn = false;

    /**
     * Table max height
     *
     * @var int|null
     */
    public $maxHeight = null;

    /**
     * The original model
     *
     * @var \Modules\Core\Models\Model
     */
    protected $model = null;

    /**
     * Table settings
     */
    protected TableSettings $settings;

    /**
     * Columns collection
     */
    protected Collection $columns;

    /**
     * Initialize new Table instance.
     */
    public function __construct(protected Builder $query, protected ResourceRequest $request)
    {
        $this->model = $query->getModel();
        $this->setIdentifier(Str::kebab(class_basename(static::class)));
        $this->setColumns($this->columns());
        $this->settings = new TableSettings(
            $this,
            $this->request->user(),
        );
        $this->boot();
    }

    /**
     * Custom boot method
     */
    public function boot(): void
    {
        //
    }

    /**
     * Provides table columns
     */
    public function columns(): array
    {
        return [];
    }

    /**
     * Set the table column
     */
    public function setColumns(array $columns): static
    {
        $this->columns = new Collection($columns);

        if ($this->withActionsColumn === true) {
            // Check if we need to add the action
            if (! $this->columns->whereInstanceOf(ActionColumn::class)->first()) {
                $this->addColumn(new ActionColumn);
            }
        }

        return $this;
    }

    /**
     * Add new column to the table
     */
    public function addColumn(Column $column): static
    {
        $this->columns->push($column);

        return $this;
    }

    /**
     * Creates the table data and return the data
     */
    public function make(): LengthAwarePaginator
    {
        $allTimeTotal = $this->getAllTimeTotal();

        $this->setSearchableFields();

        $this->query->criteria([
            $this->createTableRequestCriteria(),
            $this->createFilterRulesCriteria(),
        ]);

        // If you're combining withCount with a select statement,
        // ensure that you call withCount after the select method
        $response = $this->query->select($this->getSelectColumns())
            ->with(array_merge($this->withRelationships(), $this->with))
            ->withCount(array_merge($this->countedRelationships(), $this->withCount))
            ->paginate($this->request->integer('per_page', $this->perPage));

        return $this->parseResponse($response, $allTimeTotal);
    }

    /**
     * Get the all time total count
     */
    public function getAllTimeTotal(): int
    {
        return $this->model->count();
    }

    /**
     * Get the table request
     */
    public function getRequest(): ResourceRequest
    {
        return $this->request;
    }

    /**
     * Get the server for the table AJAX request params
     */
    public function getRequestQueryString(): array
    {
        return $this->requestQueryString;
    }

    /**
     * Set table default order by
     */
    public function orderBy(string $attribute, string $dir = 'asc'): static
    {
        $this->order[] = ['attribute' => $attribute, 'direction' => $dir];

        return $this;
    }

    /**
     * Clear the order by attributes
     */
    public function clearOrderBy(): static
    {
        $this->order = [];

        return $this;
    }

    /**
     * Add additional relations to eager load
     */
    public function with(string|array $relations): static
    {
        $this->with = array_merge($this->with, (array) $relations);

        return $this;
    }

    /**
     * Add additional countable relations to eager load
     */
    public function withCount(string|array $relations): static
    {
        $this->withCount = array_merge($this->withCount, (array) $relations);

        return $this;
    }

    /**
     * Get the table available table filters
     * Checks for custom configured filters
     */
    public function filters(ResourceRequest $request): array|Collection
    {
        return $this->filters;
    }

    /**
     * Set table available filters
     */
    public function setFilters(array|Collection $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Set the table available actions
     */
    public function setActions(array|Collection $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Available table actions
     */
    public function actions(ResourceRequest $request): array|Collection
    {
        return $this->actions;
    }

    /**
     * Get defined column by given attribute
     */
    public function getColumn(string $attribute): ?Column
    {
        return $this->columns->firstWhere('attribute', $attribute);
    }

    /**
     * Get table available columns
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * Check if the table is sorted by specific column
     */
    public function isSortingByColumn(Column $column): bool
    {
        $sortingBy = $this->request->get('order', []);
        $sortedByFields = data_get($sortingBy, '*.attribute');

        return in_array($column->attribute, $sortedByFields);
    }

    /**
     * Get the table settings for the given request
     */
    public function settings(): TableSettings
    {
        return $this->settings;
    }

    /**
     * Get the table identifier
     */
    public function identifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set table identifier
     */
    public function setIdentifier(string $key): static
    {
        $this->identifier = $key;

        return $this;
    }

    /**
     * Get additional select columns for the query
     */
    protected function addSelect(): array
    {
        return [];
    }

    /**
     * Provide the attributes that should be appended within the response
     */
    protected function appends(): array
    {
        return [];
    }

    /**
     * Get select columns
     *
     * Will return that columns only that are needed for the table
     * For example of the user made some columns not visible they won't be queried
     */
    protected function getSelectColumns(): array
    {
        $columns = $this->getUserColumns();
        $select = [];

        foreach ($columns as $column) {
            if ($column->isHidden() && ! $column->queryWhenHidden) {
                continue;
            }

            if (! $column->isRelation()) {
                if ($field = $this->getSelectableField($column)) {
                    $select[] = $field;
                }
            } elseif ($column instanceof BelongsToColumn) {
                // Select the foreign key name for the BelongsToColumn
                // If not selected, the relation won't be queried properly
                $select[] = $this->model->{$column->relationName}()->getQualifiedForeignKeyName();
            }
        }

        return array_unique(array_merge(
            $this->qualifyColumn($this->addSelect()),
            [$this->model->getQualifiedKeyName().' as '.$this->model->getKeyName()],
            $select
        ), SORT_REGULAR);
    }

    /**
     * Set the model searchable fields based on the visible columns
     */
    protected function setSearchableFields(): void
    {
        $this->query->getModel()->setSearchableFields($this->getSearchableColumns()->mapWithKeys(function ($column) {
            if ($column->isRelation()) {
                $searchableField = $column->relationName.'.'.$column->relationField;
            } else {
                $searchableField = $column->attribute;
            }

            return [$searchableField => 'like'];
        })->all());
    }

    /**
     * Filter the searchable columns
     */
    protected function getSearchableColumns(): Collection
    {
        return $this->getUserColumns()->filter(function ($column) {
            // We will check if the column is date column, as date columns are not searchable
            // as there won't be accurate results because the database dates are stored in UTC timezone
            // In this case, the filters must be used
            // Additionally we will check if is countable column and the column counts
            if ($column instanceof DateTimeColumn ||
                $column instanceof DateColumn ||
                $column instanceof Countable && $column->counts()) {
                return false;
            }

            // Relation columns with no custom query are searchable
            if ($column->isRelation()) {
                return empty($column->queryAs);
            }

            // Regular database, and also is not queried
            // with DB::raw, when querying with DB::raw, you must implement
            // custom searching criteria
            return empty($column->queryAs);
        });
    }

    /**
     * Create new TableRequestCriteria criteria instance
     */
    protected function createTableRequestCriteria(): TableRequestCriteria
    {
        return new TableRequestCriteria(
            $this->getUserColumns(),
            $this
        );
    }

    /**
     * Create new FilterRulesCriteria criteria instance
     */
    protected function createFilterRulesCriteria(): FilterRulesCriteria
    {
        return new FilterRulesCriteria(
            $this->request->get('rules'),
            $this->resolveFilters($this->request),
            $this->request
        );
    }

    /**
     * Get field by column that should be included in the table select query
     *
     * @see for $isRelationWith take a look in \Modules\Core\Table\HandlesRelations
     *
     * @param  bool  $isRelationWith Whether this field will be used for eager loading
     */
    protected function getSelectableField(Column $column, bool $isRelationWith = false): mixed
    {
        if ($column instanceof ActionColumn) {
            return null;
        }

        if (! empty($column->queryAs)) {
            return $column->queryAs;
        } elseif ($isRelationWith) {
            return $this->qualifyColumn($column->relationField, $column->relationName);
        }

        return $this->qualifyColumn($column->attribute);
    }

    /**
     * Qualify the given column
     */
    protected function qualifyColumn(string|array $column, ?string $relationName = null): array|string
    {
        if (is_array($column)) {
            return array_map(fn ($column) => $this->qualifyColumn($column, $relationName), $column);
        }

        if ($relationName) {
            return $this->model->{$relationName}()->qualifyColumn($column);
        }

        return $this->model->qualifyColumn($column);
    }

    /**
     * Get the columns for the table intended to be shown to the logged in user
     */
    protected function getUserColumns(): Collection
    {
        return $this->settings()->getColumns();
    }
}
