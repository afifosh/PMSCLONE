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

namespace Modules\Core\Table;

class BelongsToColumn extends RelationshipColumn
{
    /**
     * @var callable|null
     */
    public $orderColumnCallback;

    /**
     * Add custom order column name callback
     */
    public function orderByColumn(callable $callback): static
    {
        $this->orderColumnCallback = $callback;

        return $this;
    }
}
