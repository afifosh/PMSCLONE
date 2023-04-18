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

namespace App\Innoclapps\Criteria;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Innoclapps\QueryBuilder\Parser;
use Illuminate\Database\Eloquent\Builder;
use App\Resources\User\Filters\User as UserFilter;
use App\Innoclapps\QueryBuilder\JoinRelationParser;
use App\Innoclapps\Contracts\Repository\CriteriaInterface;
use App\Innoclapps\Contracts\Repositories\FilterRepository;
use App\Innoclapps\Contracts\Repository\RepositoryInterface;

class FilterRulesCriteria implements CriteriaInterface
{
    /**
     * The workable object
     *
     * @var \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * @var string|null
     */
    protected $identifier;

    /**
     * @var string|null
     */
    protected $view;

    /**
     * @var \App\Innoclapps\Contracts\Repositories\FilterRepository
     */
    protected $filterRepository;

    /**
     * @param array|object|null $rules The request rules
     * @param \Illuminate\Support\Collection $filters All resource available filters
     * @param \Illuminate\Http\Request
     */
    public function __construct(protected $rules, protected $filters, protected Request $request)
    {
        $this->filterRepository = resolve(FilterRepository::class);
    }

    /**
     * Apply criteria in query repository
     *
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param \App\Innoclapps\Contracts\Repository\RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $this->prepareRules();

        if (! Parser::validate($this->rules)) {
            return $model;
        }

        $this->model = $model;

        $this->setSpecialValueMe($this->rules->children);

        return $this->model->where(function ($builder) use ($model) {
            $this->createParser()->parse(
                $this->rules,
                $builder
            );

            // On the parent model query remove any global scopes
            // that are removed from the where builder instance
            // e.q. soft deleted when calling onlyTrashed
            $model->withoutGlobalScopes($builder->removedScopes());
        });
    }

    /**
     * Set the filters identifier
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set the filters view name where the rules are applied
     *
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Create the filters parser
     *
     * @return App\Innoclapps\QueryBuilder\Parser
     */
    public function createParser()
    {
        return $this->hasRulesRelations($this->rules->children)
                ? new JoinRelationParser($this->filters, $this->prepareParserJoinFields($this->rules->children))
                : new Parser($this->filters);
    }

    /**
     * Check whether the rules from the requests includes a relation
     *
     * @param object $rules
     *
     * @return boolean
     */
    protected function hasRulesRelations($rules)
    {
        $retVal = false;

        foreach ($rules as $rule) {
            if (Parser::isNested($rule)) {
                $retVal = $this->hasRulesRelations($rule->query->children);
            } else {
                // isset($rule->query->rule) - maybe the group is empty, will throw an error if not checked
                if (isset($rule->query->rule) && $this->isFieldRelation($rule->query->rule)) {
                    return true;
                }
            }
        }

        return $retVal;
    }

    /**
     * Check if field is relation e.q. contact.first_name
     *
     * @param string $name QueryBuilder Rule ID
     *
     * @return boolean
     */
    protected function isFieldRelation($name)
    {
        if (str_contains($name, '.')) {
            // Perhaps is e.q. companies.name with table prefix
            $ruleArray    = array_reverse(explode('.', $name));
            $relationName = end($ruleArray);

            // Not defined, not relation
            return $this->model->getModel()->isRelation($relationName);
        }

        return false;
    }

    /**
     * Get relation data to be used in query has
     *
     *    return $query->whereHas($relation, function ($query) use ($field, $operator, $value, $condition) {
     *       $query->where($field, $operator, $value, $condition);
     *    });
     *
     * @param string $name QueryBuilder Rule ID
     *
     * @return array
     */
    protected function getRelationFieldDataForQuery($name)
    {
        if (! $this->isFieldRelation($name)) {
            return false;
        }

        $explode  = explode('.', $name);
        $field    = array_pop($explode);
        $relation = implode('.', $explode);

        return ['field' => $field, 'relation' => $relation];
    }

    /**
     * Prepare the rules for the parser
     *
     * @return void
     */
    protected function prepareRules()
    {
        $rules = $this->rules;

        if (! $rules || count(is_array($rules) ? $rules['children'] ?? [] : $rules->children ?? []) === 0) {
            if ($this->request->has('filter_id')) {
                $rules = $this->filterRepository
                    ->forUser($this->identifier, Auth::id())
                    ->find($this->request->filter_id)?->rules;
            } elseif ($this->request->boolean('with_default_filter')) {
                $rules = $this->getDefaultFilter()?->rules;
            }
        }

        return $this->rules = is_array($rules) ? Arr::toObject($rules) : $rules;
    }

    /**
     * Set the special value me to the actual logged in user id
     *
     * This is only applied for User filter
     *
     * @return void
     */
    protected function setSpecialValueMe($rules)
    {
        foreach ($rules as $rule) {
            if (Parser::isNested($rule)) {
                $this->setSpecialValueMe($rule->query->children);
            } elseif ($this->isUserSpecialRule($rule)) {
                $rule->query->value = Auth::id();
            }
        }
    }

    /**
     * Check whether the given rule is the special user file
     *
     * @param \stdClass $rule
     *
     * @return boolean
     */
    protected function isUserSpecialRule($rule)
    {
        return isset($rule->query->rule) &&
            ! is_null($this->filters->first(function ($filter) use ($rule) {
                return $filter->field() === $rule->query->rule && $filter instanceof UserFilter;
            })) &&
            $rule->query->value === 'me';
    }

    /**
     * Get the default filter for the current request and view
     *
     * @return \App\Innoclapps\Models\Filter|null
     */
    protected function getDefaultFilter()
    {
        return $this->filterRepository->findDefaultForUser(
            $this->identifier,
            $this->view ?? $this->identifier,
            Auth::id()
        );
    }

    /**
     * Prepares the join fields for the parser
     *
     * @param \stdClass $rules
     *
     * @return array
     */
    protected function prepareParserJoinFields($rules)
    {
        $retVal = [];

        foreach ($rules as $rule) {
            if (Parser::isNested($rule)) {
                $retVal = $this->prepareParserJoinFields($rule->query->children);
            } else {
                if ($relationJoinData = $this->getRelationFieldDataForQuery($rule->query->rule)) {
                    $masterModel   = $this->model->getModel();
                    $relationModel = $masterModel->{$relationJoinData['relation']}()->getModel();

                    $retVal[$rule->query->rule] = [];

                    $retVal[$rule->query->rule]['from_table']      = $masterModel->getConnection()->getTablePrefix() . $masterModel->getTable();
                    $retVal[$rule->query->rule]['from_col']        = Str::singular($relationJoinData['relation']) . '_' . $relationModel->getKeyName();
                    $retVal[$rule->query->rule]['to_table']        = $masterModel->getConnection()->getTablePrefix() . $relationModel->getTable();
                    $retVal[$rule->query->rule]['to_col']          = $relationModel->getKeyName();
                    $retVal[$rule->query->rule]['to_value_column'] = $relationJoinData['field'];
                }
            }
        }

        return $retVal;
    }
}
