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

namespace App\Innoclapps\Menu;

use JsonSerializable;
use Illuminate\Support\Str;
use App\Innoclapps\Makeable;
use App\Innoclapps\Authorizeable;
use Illuminate\Contracts\Support\Arrayable;

class MenuItem implements Arrayable, JsonSerializable
{
    use Authorizeable, Makeable;

    /**
     * The menu item id
     *
     * @var string
     */
    public string $id;

    /**
     * Singular name e.q. Contact, translates to Create "Contact"
     *
     * @var string|null
     */
    public ?string $singularName = null;

    /**
     * Does this item should be shown on quick create section
     *
     * @var boolean
     */
    public bool $inQuickCreate = false;

    /**
     * Route for quick create
     *
     * @var string
     */
    public ?string $quickCreateRoute = null;

    /**
     * Custom quick create name
     *
     * @var string
     */
    public ?string $quickCreateName = null;

    /**
     * Badge for the sidebar item
     *
     * @var mixed
     */
    public $badge = null;

    /**
     * Badge color variant
     *
     * @var string
     */
    public string $badgeVariant = 'warning';

    /**
     * Badge color variant
     *
     * @var string|null
     */
    public ?string $keyboardShortcutChar = null;

    /**
     * Initialize new Item instance.
     *
     * @param string $name
     * @param string $route
     * @param string $icon
     * @param integer|null $position
     */
    public function __construct(public string $name, public string $route, public string $icon = '', public ?int $position = null)
    {
        $this->id = Str::slug($route);
    }

    /**
     * Set menu item id
     *
     * @param string $id
     *
     * @return static
     */
    public function id(string $id) : static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set menu item icon
     *
     * @param string $icon
     *
     * @return static
     */
    public function icon(string $icon) : static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set badge for the menu item
     *
     * @param mixed $badge
     *
     * @return static
     */
    public function badge(mixed $value) : static
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set badge variant
     *
     * @param string $value
     *
     * @return static
     */
    public function badgeVariant(string $value) : static
    {
        $this->badgeVariant = $value;

        return $this;
    }

    /**
     * Get badge for the menu item
     *
     * @return mixed
     */
    public function getBadge() : mixed
    {
        if ($this->badge instanceof \Closure) {
            return ($this->badge)();
        }

        return $this->badge;
    }

    /**
     * Set menu item position
     *
     * @param integer $position
     *
     * @return static
     */
    public function position(int $position) : static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Set menu item singular name
     *
     * @param string $singular
     *
     * @return static
     */
    public function singularName(string $singular) : static
    {
        $this->singularName = $singular;

        return $this;
    }

    /**
     * Whether this item should be also included in the quick create section
     *
     * @param boolean $bool
     *
     * @return static
     */
    public function inQuickCreate(bool $bool = true) : static
    {
        $this->inQuickCreate = $bool;

        return $this;
    }

    /**
     * Set the keyboard shortcut character
     *
     * @param string $char
     *
     * @return static
     */
    public function keyboardShortcutChar(string $char) : static
    {
        $this->keyboardShortcutChar = $char;

        return $this;
    }

    /**
     * Custom quick create route
     * Default route is e.q. contacts/create
     *
     * @param string $route
     *
     * @return static
     */
    public function quickCreateRoute(string $route) : static
    {
        $this->quickCreateRoute = $route;

        return $this;
    }

    /**
     * Custom quick create name
     *
     * @param string $name
     *
     * @return static
     */
    public function quickCreateName(string $name) : static
    {
        $this->quickCreateName = $name;

        return $this;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'singularName'         => $this->singularName,
            'route'                => $this->route,
            'icon'                 => $this->icon,
            'inQuickCreate'        => $this->inQuickCreate,
            'quickCreateRoute'     => $this->quickCreateRoute,
            'quickCreateName'      => $this->quickCreateName,
            'position'             => $this->position,
            'badge'                => $this->getBadge(),
            'badgeVariant'         => $this->badgeVariant,
            'keyboardShortcutChar' => $this->keyboardShortcutChar,
        ];
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
}
