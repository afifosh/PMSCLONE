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

namespace App\Innoclapps\QueryBuilder;

use stdClass;
use Exception;
use Illuminate\Support\Collection;
use App\Innoclapps\Filters\OperandFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Innoclapps\ProvidesBetweenArgumentsViaString;
use App\Innoclapps\QueryBuilder\Exceptions\QueryBuilderException;

class Parser
{
    use ParserTrait,
        ProvidesBetweenArgumentsViaString;

    /**
     * Initialize new Parser instance.
     *
     * @param \Illimunate\Support\Collection $filters
     */
    public function __construct(protected Collection $filters)
    {
    }

    /**
     * Build a query based on JSON that has been passed into the function, onto the builder passed into the function.
     *
     * @param \stdClass $query
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @throws QueryBuilderException
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function parse($query, Builder $builder)
    {
        if (! static::validate($query)) {
            return $builder;
        }

        return $this->loopThroughRules($query->children, $builder, $query->condition);
    }

    /**
     * Called by parse, loops through all the rules to find out if nested or not.
     *
     * @param array $rules
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $queryCondition
     *
     * @throws QueryBuilderException
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function loopThroughRules(array $rules, Builder $builder, $queryCondition = 'AND')
    {
        foreach ($rules as $rule) {
            if ($rule->type == 'rule') {
                /*
                * The field must exist in our list and be allowed.
                */
                if (! $this->fieldExistsAndItsAllowed($this->whitelistedRules(), $rule->query->rule)) {
                    continue;
                }

                $builder = $this->makeQuery($builder, $rule, $queryCondition);
            }

            if (static::isNested($rule)) {
                $builder = $this->createNestedQuery($builder, $rule, $queryCondition);
            }
        }

        return $builder;
    }

    /**
     * Create nested queries
     *
     * When a rule is actually a group of rules, we want to build a nested query
     * with the specified condition (AND/OR)
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \stdClass $rule
     * @param string|null $condition
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function createNestedQuery(Builder $builder, stdClass $rule, $condition = null)
    {
        if ($condition === null) {
            $condition = $rule->query->condition;
        }

        $condition = $this->validateCondition($condition);

        return $builder->where(function ($query) use (&$rule, &$builder, &$condition) {
            foreach ($rule->query->children as $loopRule) {
                $method = 'makeQuery';
                if (static::isNested($loopRule)) {
                    $method = 'createNestedQuery';
                }

                if ($rule->type == 'rule' &&
                    ! $this->fieldExistsAndItsAllowed($this->whitelistedRules(), $rule->query->rule)) {
                    continue;
                }

                $builder = $this->{$method}($query, $loopRule, $rule->query->condition);
            }
        }, null, null, $condition);
    }

    /**
     * Determine if a particular rule is actually a group of other rules.
     *
     * @param \stdClass $rule
     *
     * @return boolean
     */
    public static function isNested($rule)
    {
        return (bool) (isset($rule->query->children) &&
            is_array($rule->query->children) &&
            count($rule->query->children) > 0);
    }

    /**
     * Check if a given rule is correct.
     *
     * Just before making a query for a rule, we want to make sure that
     * the field operator and value are set
     *
     * @param \stdClass $rule
     *
     * @return boolean
     */
    protected function checkRuleCorrect(stdClass $rule)
    {
        if (! isset($rule->query->operator, $rule->query->rule, $rule->query->type)) {
            return $this->findFilterByRule($rule)->isStatic();
        }

        return isset($this->operators[$rule->query->operator]);
    }

    /**
     * Give back the correct value when we don't accept one.
     *
     * @param \stdClass $rule
     *
     * @return null|string
     */
    protected function operatorValueWhenNotAcceptingOne(stdClass $rule)
    {
        if ($rule->query->operator == 'is_empty' || $rule->query->operator == 'is_not_empty') {
            return '';
        }

        return null;
    }

    /**
     * Ensure that the value for a field is correct.
     *
     * Append/Prepend values for SQL statements, etc.
     *
     * @param $operator
     * @param \stdClass $rule
     * @param $value
     *
     * @throws QueryBuilderException
     *
     * @return string
     */
    protected function getCorrectValue($operator, stdClass $rule, $value)
    {
        $field        = $rule->query->rule;
        $sqlOperator  = $this->operator_sql[$rule->query->operator];
        $requireArray = $this->operatorRequiresArray($operator);

        if ($this->isDateIsOperator($rule)) {
            $value = $this->getCorrectValueWhenIsDateIsOperator($value);
        } elseif ($this->isDateWasOperator($rule)) {
            $value = $this->getCorrectValueWhenIsDateWasOperator($value);
        } else {
            $value = $this->enforceArrayOrString($requireArray, $value, $field);

            if ($rule->query->type == 'date') {
                $value = $this->getDateCarbonValueByRequestedValue($value, $this->findFilterByRule($rule));
            }
        }

        return $this->appendOperatorIfRequired($requireArray, $value, $sqlOperator);
    }

    /**
     * Take a particular rule and make build something that the QueryBuilder would be proud of.
     *
     * Make sure that all the correct fields are in the rule object then add the expression to
     * the query that was given by the user to the QueryBuilder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \stdClass $rule
     * @param string $queryCondition and/or...
     *
     * @throws QueryBuilderException
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function makeQuery(Builder $builder, stdClass $rule, $queryCondition = 'AND')
    {
        $value = $this->getValueForQueryFromRule($rule);

        return $this->convertIncomingQBtoQuery($builder, $rule, $value, $queryCondition);
    }

    /**
     * Convert an incomming rule from QueryBuilder to the Eloquent Querybuilder
     *
     * (This used to be part of makeQuery, where the name made sense, but I pulled it
     * out to reduce some duplicated code inside JoinSupportingQueryBuilder)
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \stdClass $rule
     * @param mixed $value the value that needs to be queried in the database.
     * @param string $queryCondition and/or...
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function convertIncomingQBtoQuery(Builder $builder, stdClass $rule, $value, $queryCondition = 'AND')
    {
        $filter    = $this->findFilterByRule($rule);
        $condition = strtolower($queryCondition);

        if ($filter->isStatic()) {
            return $this->makeQueryWhenCustom($builder, $filter, $rule, [], null, $condition);
        } elseif ($filter instanceof OperandFilter) {
            $rule = $this->prepareRuleWhenViaOperand($rule, $filter);
        }

        /*
         * Convert the Operator (LIKE/NOT LIKE/GREATER THAN) given to us by QueryBuilder
         * into on one that we can use inside the SQL query
         */
        $sqlOperator = $this->operator_sql[$rule->query->operator];
        $operator    = $sqlOperator['operator'];

        if ($filter->tapCallback) {
            call_user_func_array($filter->tapCallback, [$builder, $value, $condition, $rule, $this]);
        }

        if ($filter->hasCustomQuery()) {
            return $this->makeQueryWhenCustom($builder, $filter, $rule, $sqlOperator, $value, $condition);
        } elseif ($filter instanceof OperandFilter) {
            return $this->makeQueryWhenHasOperands($builder, $filter, $rule, $operator, $value, $condition);
        } elseif ($this->ruleCountsRelation($filter)) {
            return $this->makeQueryWhenCountableRelation($builder, $filter, $rule, $operator, $value, $condition);
        } elseif ($this->isDateRule($rule)) {
            return $this->makeQueryWhenDate($builder, $filter, $rule, $operator, $value, $condition);
        }

        return $this->convertToQuery($builder, $rule, $value, $operator, $condition);
    }

    /**
     * Convert to regular query helper
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function convertToQuery(Builder $builder, $rule, $value, $operator, $condition)
    {
        if ($this->operatorRequiresArray($operator)) {
            return $this->makeQueryWhenArray($builder, $rule, $operator, $value, $condition);
        } elseif ($this->operatorIsNull($operator)) {
            return $this->makeQueryWhenNull($builder, $rule, $operator, $condition);
        }

        return $builder->where(
            $this->getQueryColumn($rule, $builder),
            $operator,
            $value,
            $condition
        );
    }

    /**
     * Ensure that the value is correct for the rule, try and set it if it's not.
     *
     * @param \stdClass $rule
     *
     * @throws QueryBuilderException
     *
     * @return mixed
     */
    protected function getValueForQueryFromRule(stdClass $rule)
    {
        if ($this->findFilterByRule($rule)->isStatic()) {
            return null;
        }

        /*
         * Make sure most of the common fields from the QueryBuilder have been added.
         */
        $value = $this->getRuleValue($rule);

        /*
         * If the SQL Operator is set not to have a value, make sure that we set the value to null.
         */
        if ($this->operators[$rule->query->operator]['accept_values'] === false) {
            return $this->operatorValueWhenNotAcceptingOne($rule);
        }

        /*
         * Convert the Operator (LIKE/NOT LIKE/GREATER THAN) given to us by QueryBuilder
         * into on one that we can use inside the SQL query
         */
        $sqlOperator = $this->operator_sql[$rule->query->operator];
        $operator    = $sqlOperator['operator'];
        /*
         * \o/ Ensure that the value is an array only if it should be.
         */

        return $this->getCorrectValue($operator, $rule, $value);
    }

    /**
     * Get between dates when rule is DATE and IS operator is selected
     *
     * @param string $value
     *
     * @throws QueryBuilderException
     *
     * @return array
     */
    protected function getCorrectValueWhenIsDateIsOperator($value)
    {
        try {
            return $this->getBetweenArguments($value);
        } catch (Exception $e) {
            throw new QueryBuilderException($e->getMessage());
        }
    }

    /**
     * Get between dates when rule is DATE and WAS operator is selected
     *
     * @param string $value
     *
     * @throws QueryBuilderException
     *
     * @return array
     */
    protected function getCorrectValueWhenIsDateWasOperator($value)
    {
        try {
            return $this->getBetweenArguments($value);
        } catch (Exception $e) {
            throw new QueryBuilderException($e->getMessage());
        }
    }

    /**
     * get a value for a given rule.
     *
     * throws an exception if the rule is not correct.
     *
     * @param \stdClass $rule
     *
     * @throws QueryBuilderException
     *
     * @return mixed
     */
    protected function getRuleValue(stdClass $rule)
    {
        if (! $this->checkRuleCorrect($rule)) {
            throw new QueryBuilderException('Rule not correct');
        }

        return $rule->query->value;
    }

    /**
     * Make query when the rule has operands
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \App\Innoclapps\Filters\Filter $filter
     * @param \stdClass $rule
     * @param string $operator
     * @param mixed $value
     * @param string $condition
     *
     * @throws QueryBuilderException
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function makeQueryWhenHasOperands(Builder $builder, $filter, $rule, $operator, $value, $condition)
    {
        if ($this->ruleCountsRelation($rule->operand->rule)) {
            return $this->makeQueryWhenCountableRelation($builder, $rule->operand->rule, $rule, $operator, $value, $condition);
        }

        return $this->makeQuery($builder, $rule, $condition);
    }

    /**
     * Prepare the rule when it's via operand
     *
     * @param \stdClass $rule
     * @param \App\Innoclapps\Filters\FilterOperand $filter
     *
     * @throws QueryBuilderException
     *
     * @return \stdClass
     */
    protected function prepareRuleWhenViaOperand($rule, $filter)
    {
        $operand = $filter->findOperand($rule->query->operand);

        if (! $operand) {
            throw new QueryBuilderException('Selected operand not found.');
        }

        // We will modify the actual rule value to be queryable
        $rule->operand     = $operand;
        $rule->query->type = $operand->rule->type();
        $rule->query->rule = $operand->rule->field();

        // Set the selected operand for all cases, not used ATM
        $filter->setOperand($rule->query->operand);

        return $rule;
    }

    /**
     * Get the while listed fields for the query builder only
     *
     * @return array
     */
    protected function whitelistedRules()
    {
        return $this->filters->map(function ($filter) {
            return $filter->field();
        })->all();
    }

    /**
     * Find filter by a given rule
     *
     * @param stdClass $rule
     *
     * @return \App\Innoclapps\Filters\Filter|null
     */
    protected function findFilterByRule($rule)
    {
        if (isset($rule->operand) && $rule->operand) {
            return $rule->operand->rule->field() === $rule->query->rule ? $rule->operand->rule : null;
        }

        return $this->filters->first(fn ($filter) => $filter->field() === $rule->query->rule);
    }
}
