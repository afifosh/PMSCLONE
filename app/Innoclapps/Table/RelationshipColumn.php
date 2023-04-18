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

use Illuminate\Support\Str;

class RelationshipColumn extends Column
{
    /**
     * Attributes to append with the response
     */
    public array $appends = [];

    /**
     * The relation name
     */
    public string $relationName;

    /**
     * The relation field
     */
    public ?string $relationField;

    /**
     * Additional fields to select
     */
    public array $relationSelectColumns = [];

    /**
     * Initialize new RelationshipColumn instance.
     */
    public function __construct(string $name, ?string $attribute, ?string $label = null)
    {
        // The relation names for front-end are returned in snake case format.
        parent::__construct(Str::snake($name), $label);

        $this->relationName  = $name;
        $this->relationField = $attribute;
    }

    /**
     * Additional select for a relation
     *
     * For relation e.q. MorphToManyColumn::make('contacts', 'first_name', 'Contacts')->select(['avatar', 'email'])
     */
    public function select(array|string $fields) : static
    {
        $this->relationSelectColumns = array_merge(
            $this->relationSelectColumns,
            (array) $fields
        );

        return $this;
    }

    /**
     * Set attributes to appends in the model
     */
    public function appends(array|string $attributes) : static
    {
        $this->appends = (array) $attributes;

        return $this;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'relationField' => $this->relationField,
        ]);
    }
}