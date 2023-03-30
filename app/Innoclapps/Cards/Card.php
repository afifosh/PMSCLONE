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

namespace App\Innoclapps\Cards;

use JsonSerializable;
use Illuminate\Support\Str;
use App\Innoclapps\Makeable;
use App\Innoclapps\Authorizeable;
use App\Innoclapps\RangedElement;
use App\Innoclapps\MetableElement;

// @ todo, add Authorizeable tests and general test

abstract class Card extends RangedElement implements JsonSerializable
{
    use Authorizeable,
        MetableElement,
        Makeable;

    /**
     * The card name/title that will be displayed
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Explanation about the card data
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * The width of the card (Tailwind width class e.q. w-1/2, w-1/3, w-full)
     *
     * @var string
     */
    public string $width = 'w-full lg:w-1/2';

    /**
     * Indicates that the card should be shown only dashboard
     *
     * @var boolean
     */
    public bool $onlyOnDashboard = false;

    /**
     * Indicates that the card should be shown only on index
     *
     * @var boolean
     */
    public bool $onlyOnIndex = false;

    /**
     * Indicates that the card should refreshed when action is executed
     *
     * @var boolean
     */
    public bool $refreshOnActionExecuted = false;

    /**
     * Indicates whether user can be selected
     *
     * @var boolean|int|callable
     */
    public mixed $withUserSelection = false;

    /**
     * Define the card component used on front end
     *
     * @return string
     */
    abstract public function component() : string;

    /**
     * The card human readable name
     *
     * @return string|null
     */
    public function name() : ?string
    {
        return $this->name;
    }

    /**
     * Get the card explanation
     *
     * @return string|null
     */
    public function description() : ?string
    {
        return $this->description;
    }

    /**
     * Set that the card should be shown only dashboard
     *
     * @return static
     */
    public function onlyOnDashboard() : static
    {
        $this->onlyOnDashboard = true;

        return $this;
    }

    /**
     * Set that the card should be shown only on index
     *
     * @return static
     */
    public function onlyOnIndex() : static
    {
        $this->onlyOnIndex = true;

        return $this;
    }

    /**
     * Get the URI key for the card.
     *
     * @return string
     */
    public function uriKey() : string
    {
        return Str::kebab(class_basename(get_called_class()));
    }

    /**
     * Set the card width class
     *
     * @param string $width
     *
     * @return static
     */
    public function width($width) : static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set refresh on action executed for the card
     *
     * @param boolean $value
     *
     * @return static
     */
    public function refreshOnActionExecuted(bool $value = true) : static
    {
        $this->refreshOnActionExecuted = $value;

        return $this;
    }

    /**
     * Card help text
     *
     * @param string|null $text
     *
     * @return static
     */
    public function help(?string $text) : static
    {
        $this->withMeta(['help' => $text]);

        return $this;
    }

    /**
     * Set card user selection
     *
     * @param boolean|int|callable $value
     *
     * @return static
     */
    public function withUserSelection(bool|int|callable $value = true) : static
    {
        $this->withUserSelection = $value;

        return $this;
    }

    /**
     * Get the users for selection
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function users()
    {
        //
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
            'uriKey'            => $this->uriKey(),
            'component'         => $this->component(),
            'name'              => $this->name(),
            'description'       => $this->description(),
            'width'             => $this->width,
            'withUserSelection' => is_callable($this->withUserSelection) ?
                call_user_func($this->withUserSelection, $this) :
                $this->withUserSelection,
            'users'                   => $this->users(),
            'refreshOnActionExecuted' => $this->refreshOnActionExecuted,
            'data'                    => method_exists($this, 'getData') ? $this->getData() : [],
        ], $this->meta());
    }
}
