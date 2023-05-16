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

namespace Modules\Core\Placeholders;

use Closure;
use JsonSerializable;
use Modules\Core\Makeable;

abstract class Placeholder implements JsonSerializable
{
    use Makeable;

    /**
     * Indicates the starting interpolation
     */
    public string $interpolationStart = '{{';

    /**
     * Indicates the ending interpolation
     */
    public string $interpolationEnd = '}}';

    /**
     * The placeholder description
     */
    public ?string $description = null;

    /**
     * The placeholder tag
     */
    public string $tag;

    /**
     * Custom value callback
     * e.q. can be used value(function(){})
     *
     * @var null|callable
     */
    public $valueCallback;

    /**
     * Initialize the placeholder
     *
     * @param  \Closure|string  $value Pass value via the constructor
     */
    public function __construct($value = null)
    {
        if ($value) {
            $this->value($value);
        }

        $this->boot();
    }

    /**
     * Format the placeholder
     *
     *
     * @return string
     */
    abstract public function format(?string $contentType = null);

    /**
     * Boot the mail placeholder
     * e.q. can be used to set custom description or tag
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Change the placeholder starting interpolation
     */
    public function withStartInterpolation(string $value): static
    {
        $this->interpolationStart = $value;

        return $this;
    }

    /**
     * Change the placeholder ending interpolation
     */
    public function withEndInterpolation(string $value): static
    {
        $this->interpolationEnd = $value;

        return $this;
    }

    /**
     * Set the placeholder value
     *
     * @param  mixed  $value
     */
    public function value($value): static
    {
        if ($value instanceof Closure) {
            $this->valueCallback = $value;
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * Set placeholder tag
     */
    public function tag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Set placeholder description
     */
    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Serialize the placeholder for the front end
     */
    public function jsonSerialize(): array
    {
        return [
            'tag' => $this->tag,
            'description' => $this->description,
            'interpolation_start' => $this->interpolationStart,
            'interpolation_end' => $this->interpolationEnd,
        ];
    }

    /**
     * Dynamically access a property for the value
     *
     * E.q. can be used with provided callback on value or __constructor
     * Helpful when the data is not yet defined in the Mailable to prevent
     * throwing errors when fetching the placeholders tags for the front end
     *
     * In this case, before this code is executed, value will be null and we
     * will use the callback to get the value
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'value' && $this->valueCallback) {
            return call_user_func($this->valueCallback);
        }
    }
}
