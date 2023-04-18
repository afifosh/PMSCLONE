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

namespace App\Innoclapps\Table;

use Closure;
use JsonSerializable;
use App\Innoclapps\Makeable;
use App\Innoclapps\Authorizeable;
use App\Innoclapps\MetableElement;
use App\Innoclapps\Contracts\Countable;
use Illuminate\Database\Query\Expression;
use Illuminate\Contracts\Support\Arrayable;

class Column implements Arrayable, JsonSerializable
{
    use Authorizeable,
        MetableElement,
        Makeable;

    /**
     * Custom query for this field
     */
    public Expression|Closure|string|null $queryAs = null;

    /**
     * Indicates whether the column is sortable
     */
    public bool $sortable = true;

    /**
     * Indicates whether to add the column contents in the front end as HTML
     */
    public bool $asHtml = false;

    /**
     * Indicates whether the column is hidden
     */
    public ?bool $hidden = null;

    /**
     * Indicates special columns and some actions are disabled
     */
    public bool $primary = false;

    /**
     * Table th/td min width
     */
    public ?string $minWidth = '200px';

    /**
     * The column default order
     */
    public ?int $order = null;

    /**
     * Indicates whether to include the column in the query when it's hidden
     */
    public bool $queryWhenHidden = false;

    /**
     * Custom column display formatter
     */
    public ?Closure $displayAs = null;

    /**
     * Column help text
     */
    public ?string $helpText = null;

    /**
     * Data heading component
     */
    public string $component = 'table-data-column';

    /**
     * Initialize new Column instance.
     *
     * @param string|null $attribute
     * @param string|null $label
     */
    public function __construct(public ?string $attribute = null, public ?string $label = null)
    {
    }

    /**
     * Custom query for this column
     */
    public function queryAs(Expression|Closure|string $queryAs) : static
    {
        $this->queryAs = $queryAs;

        return $this;
    }

    /**
     * Set column name/label
     */
    public function label(?string $label) : static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set whether the column is sortable
     */
    public function sortable(bool $bool = true) : static
    {
        $this->sortable = $bool;

        return $this;
    }

    /**
     * Set the column help text
     */
    public function help(?string $text) : static
    {
        $this->helpText = $text;

        return $this;
    }

    /**
     * Check whether the column is sortable
     */
    public function isSortable() : bool
    {
        return $this->sortable === true;
    }

    /**
     * Add the column content as HTML
     */
    public function asHtml(bool $bool = true) : static
    {
        $this->asHtml = $bool;

        return $this;
    }

    /**
     * Set column visibility
     */
    public function hidden(bool $bool = true) : static
    {
        $this->hidden = $bool;

        return $this;
    }

    /**
     * Check whether the column is hidden
     */
    public function isHidden() : bool
    {
        return $this->hidden === true;
    }

    /**
     * Set the column as primary
     */
    public function primary(bool $bool = true) : static
    {
        $this->primary = $bool;

        return $this;
    }

    /**
     * Check whether the column is primary
     */
    public function isPrimary() : bool
    {
        return $this->primary === true;
    }

    /**
     * Set column td custom class
     */
    public function tdClass(string $class) : static
    {
        return $this->withMeta([__FUNCTION__ => $class]);
    }

    /**
     * Set column th custom class
     */
    public function thClass(string $class) : static
    {
        return $this->withMeta([__FUNCTION__ => $class]);
    }

    /**
     * Set column th/td width
     */
    public function width($width) : static
    {
        return $this->withMeta([__FUNCTION__ => $width]);
    }

    /**
     * Set column th/td min width
     */
    public function minWidth(?string $minWidth) : static
    {
        $this->minWidth = $minWidth;

        return $this;
    }

    /**
     * Set the column default order
     */
    public function order(int $order) : static
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Whether to select/query the column when the column hidden
     */
    public function queryWhenHidden(bool $bool = true) : static
    {
        $this->queryWhenHidden = $bool;

        return $this;
    }

    /**
     * Custom column formatter
     */
    public function displayAs(Closure $callback) : static
    {
        $this->displayAs = $callback;

        return $this;
    }

    /**
     * Check whether a column can count relations
     */
    public function isCountable() : bool
    {
        return $this instanceof Countable;
    }

    /**
     * Get the column data component
     */
    public function component() : string
    {
        return $this->component;
    }

    /**
     * Set the column data component
     */
    public function useComponent(string $component) : static
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Check whether the column is a relation
     */
    public function isRelation() : bool
    {
        return $this instanceof RelationshipColumn;
    }

    /**
     * Center the column heading and data
     */
    public function centered(bool $value = true) : static
    {
        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge([
            'attribute' => $this->attribute,
            'label'     => $this->label,
            'sortable'  => $this->isSortable(),
            'asHtml'    => $this->asHtml,
            'hidden'    => $this->isHidden(),
            'primary'   => $this->isPrimary(),
            'component' => $this->component(),
            'minWidth'  => $this->minWidth,
            'order'     => $this->order,
            'helpText'  => $this->helpText,
            // 'isCountable' => $this->isCountable(),
        ], $this->meta());
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
}
