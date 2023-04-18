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

namespace App\Innoclapps\Workflow;

use Closure;
use JsonSerializable;
use ReflectionFunction;
use Illuminate\Support\Str;
use App\Innoclapps\Contracts\Workflow\FieldChangeTrigger;

abstract class Trigger implements JsonSerializable
{
    /**
     * Registered actions executing callbacks
     *
     * Since the actions can be queued, we will register them in the trigger
     * because the trigger is not serialized, if we register them in the action itself
     * an error will be thrown that closures cannot be serialized
     *
     * @var array
     */
    protected static array $actionExecutingCallbacks = [];

    /**
     * Provide the trigger available actions
     *
     * @return array
     */
    abstract public function actions() : array;

    /**
     * Trigger name
     *
     * @return string
     */
    public static function name() : string
    {
        return Str::title(Str::snake(class_basename(get_called_class()), ' '));
    }

    /**
     * Get single action from the workflow
     *
     * @param string $action
     *
     * @return \App\Innoclapps\Workflows\Action
     */
    public function getAction($action) : ?Action
    {
        return $this->getActions()->whereInstanceOf($action)->first();
    }

    /**
     * Get the trigger actions
     *
     * @return App\Innoclapps\Workflow\ActionsCollection
     */
    public function getActions() : ActionsCollection
    {
        return (new ActionsCollection($this->actions()))->each->setTrigger($this);
    }

    /**
     * Register new action executing event
     *
     * @param string $action
     * @param \Closure $callback
     *
     * @return void
     */
    public static function registerActionExecutingEvent(string $action, Closure $callback) : void
    {
        $key = $action . '-' . static::identifier();

        if (! isset(static::$actionExecutingCallbacks[$key])) {
            static::$actionExecutingCallbacks[$key] = [];
        }

        static::$actionExecutingCallbacks[$key] = collect(static::$actionExecutingCallbacks[$key])
            ->reject(function ($existing) use ($callback) {
                // https://askto.pro/question/how-to-compare-two-closure-objects-in-php
                // Do not allow duplicate execution register, we will check the start and end line of the callables
                $refFunc1 = new ReflectionFunction($existing);
                $refFunc2 = new ReflectionFunction($callback);

                return [
                    $refFunc1->getEndLine(),
                    $refFunc1->getEndLine(),
                ] === [
                    $refFunc2->getEndLine(),
                    $refFunc2->getEndLine(),
                ];
            })
            ->push($callback)
            ->all();
    }

    /**
     * Run the given action execution callbacks
     *
     * @param \App\Innoclapps\Workflow\Action $action
     *
     * @return void
     */
    public function runExecutionCallbacks(Action $action) : void
    {
        $key = $action::class . '-' . static::identifier();

        foreach (static::$actionExecutingCallbacks[$key] ?? [] as $callback) {
            call_user_func($callback, $action);
        }
    }

    /**
     * Trigger identifier
     *
     * @return string
     */
    public static function identifier() : string
    {
        return get_called_class();
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_merge([
            'identifier' => static::identifier(),
            'name'       => static::name(),
            'actions'    => $this->getActions(),
        ], $this instanceof FieldChangeTrigger ? ['change_field' => static::changeField()] : []);
    }
}
