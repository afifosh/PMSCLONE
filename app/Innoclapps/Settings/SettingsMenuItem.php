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

namespace App\Innoclapps\Settings;

use JsonSerializable;
use App\Innoclapps\Makeable;
use Illuminate\Contracts\Support\Arrayable;

class SettingsMenuItem implements JsonSerializable, Arrayable
{
    use Makeable;

    /**
     * Item children
     *
     * @var array
     */
    protected array $children = [];

    /**
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @var int|null
     */
    public ?int $order = null;

    /**
     * Create new SettingsMenuItem instance.
     *
     * @param string $title
     * @param string|null $route
     * @param string|null $icon
     */
    public function __construct(protected string $title, protected ?string $route = null, protected ?string $icon = null)
    {
    }

    /**
     * Set the menu item unique identifier
     *
     * @param string $id
     *
     * @return static
     */
    public function setId(string $id) : static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the menu item unique identifier
     *
     * @return static|null
     */
    public function getId() : ?string
    {
        return $this->id;
    }

    /**
     * Set the item icon
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
     * Set the item order
     *
     * @param string $icon
     *
     * @return static
     */
    public function order(int $order) : static
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Register child menu item
     *
     * @param self $item
     * @param string $id
     *
     * @return static
     */
    public function withChild(self $item, string $id) : static
    {
        $this->children[$id] = $item->setId($id);

        return $this;
    }

    /**
     * Set the item child items
     *
     * @param array $items
     *
     * @return static
     */
    public function setChildren(array $items) : static
    {
        $this->children = $items;

        return $this;
    }

    /**
     * Get the item child items
     *
     * @param array $items
     *
     * @return static
     */
    public function getChildren() : array
    {
        return collect($this->children)->sortBy('order')->values()->all();
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'route'    => $this->route,
            'icon'     => $this->icon,
            'children' => $this->getChildren(),
            'order'    => $this->order,
        ];
    }

    /**
     * Prepare the item for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
}
