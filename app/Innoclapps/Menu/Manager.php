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

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Manager
{
    /**
     * Hold the main menu items
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Register menu item(s)
     *
     * @param \App\Innoclapps\Menu\MenuItem|array $items
     *
     * @return static
     */
    public function register(MenuItem|array $items) : static
    {
        foreach (Arr::wrap($items) as $item) {
            $this->registerItem($item);
        }

        return $this;
    }

    /**
     * Register a single menu item
     *
     * @param \App\Innoclapps\Menu\MenuItem $item
     *
     * @return static
     */
    public function registerItem(MenuItem $item) : static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get all registered menu items
     *
     * @return \Illuminate\Support\Collection
     */
    public function get() : Collection
    {
        $items = (new Collection($this->items))->map(fn ($item) => $this->checkQuickCreateProperties($item));

        $ordered = $this->checkPositions($items);

        return $ordered->filter->authorizedToSee()->values();
    }

    /**
     * Clears all menu items
     *
     * @return static
     */
    public function clear() : static
    {
        $this->items = [];

        return $this;
    }

    /**
     * Check if order is set and sort the items
     *
     * @param \Illuminate\Support\Collection $items
     *
     * @return \Illuminate\Support\Collection
     */
    protected function checkPositions(Collection $items) : Collection
    {
        /**
         * If there is no position set, add the index + 5
         */
        $items->each(function ($item, $index) {
            if (! $item->position) {
                $item->position($index + 10);
            }
        });

        /**
         * Sort the items with the actual order
         */
        return $this->sort($items);
    }

    /**
     * Check quick create properties and add default props
     *
     * @param \App\Innoclapps\Menu\MenuItem $item
     *
     * @return \App\Innoclapps\Menu\MenuItem
     */
    protected function checkQuickCreateProperties(MenuItem $item) : MenuItem
    {
        if ($item->inQuickCreate) {
            if (! $item->quickCreateRoute) {
                $item->quickCreateRoute(rtrim($item->route, '/') . '/' . 'create');
            }

            if (! $item->quickCreateName) {
                $item->quickCreateName($item->singularName ?? $item->name);
            }
        }

        return $item;
    }

    /**
     * Sort the items
     *
     * @param \Illuminate\Support\Collection $items
     *
     * @return \Illuminate\Support\Collection
     */
    protected function sort(Collection $items) : Collection
    {
        return $items->sort(function ($a, $b) {
            if ($a->position == $b->position) {
                return 0;
            }

            return ($a->position < $b->position) ? -1 : 1;
        })->values();
    }
}
