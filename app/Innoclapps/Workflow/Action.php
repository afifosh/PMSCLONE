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
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Contracts\Workflow\ModelTrigger;

abstract class Action implements JsonSerializable
{
    /**
     * The data intended for the action
     *
     * @var object|null
     */
    protected $data;

    /**
     * The trigger the action is composed from
     *
     * @var \App\Innoclapps\Workflow\Trigger|null
     */
    protected $trigger;

    /**
     * Provide the action name
     *
     * @return string
     */
    abstract public static function name() : string;

    /**
     * Run the trigger
     *
     * @return mixed
     */
    abstract public function run();

    /**
     * Action available fields
     *
     * @return array
     */
    public function fields() : array
    {
        return [];
    }

    /**
     * Check whether the action can be executed
     *
     * @return boolean
     */
    public static function allowedForExecution() : bool
    {
        return Innoclapps::importStatus() === false;
    }

    /**
     * Check whether the action is triggered via model
     *
     * @return boolean
     */
    public function viaModelTrigger() : bool
    {
        return $this->trigger() instanceof ModelTrigger;
    }

    /**
     * Get the action trigger
     *
     * @return \App\Innoclapps\Workflow\Trigger|\App\Innoclapps\Contracts\Workflow\ModelTrigger|\App\Innoclapps\Contracts\Workflow\EventTrigger|null
     */
    public function trigger() : ?Trigger
    {
        return $this->trigger;
    }

    /**
     * Set the action trigger
     *
     * @param \App\Innoclapps\Workflow\Trigger $trigger
     *
     * @return static
     */
    public function setTrigger(Trigger $trigger) : static
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Register execution callback
     *
     * @param \Closure $callback
     *
     * @return static
     */
    public function executing(Closure $callback) : static
    {
        $trigger = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'];

        $trigger::registerActionExecutingEvent(get_called_class(), $callback);

        return $this;
    }

    /**
     * Get the action identifier
     *
     * @return string
     */
    public static function identifier() : string
    {
        return get_called_class();
    }

    /**
     * Set the trigger data
     *
     * @param array $data
     *
     * @return $self
     */
    public function setData($data) : static
    {
        $this->data = (object) $data;

        return $this;
    }

    /**
     * Determine if an attribute exists on data.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->data->{$key});
    }

    /**
     * Dynamically get properties from the data.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->data->{$key};
    }

    /**
     * Dynamically set properties to the data.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data->{$name} = $value;
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'identifier' => static::identifier(),
            'name'       => static::name(),
            'fields'     => $this->fields(),
        ];
    }
}
