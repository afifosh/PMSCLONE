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

namespace Modules\Core\Card;

class TableCard extends Card
{
    /**
     * The primary key for the table row
     */
    protected string $primaryKey = 'id';

    /**
     * Define the card component used on front end
     */
    public function component(): string
    {
        return 'card-table';
    }

    /**
     * Provide the table fields
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * Provide the table data
     */
    public function items(): iterable
    {
        return [];
    }

    /**
     * Table empty text
     */
    public function emptyText(): ?string
    {
        return null;
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'fields' => $this->fields(),
            'items' => $this->items(),
            'emptyText' => $this->emptyText(),
            'primaryKey' => $this->primaryKey,
        ]);
    }
}
