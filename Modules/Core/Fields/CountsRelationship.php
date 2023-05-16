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

namespace Modules\Core\Fields;

use Illuminate\Support\Str;

trait CountsRelationship
{
    /**
     * Indicates whether the relation will be counted
     */
    public bool $count = false;

    /**
     * Indicates that the relation will be counted
     */
    public function count(): static
    {
        $this->count = true;
        $this->attribute = Str::snake($this->attribute).'_count';

        return $this;
    }

    /**
     * Check whether the field counts the relation
     */
    public function counts(): bool
    {
        return $this->count === true;
    }

    /**
     * Get the count key
     */
    public function countKey(): string
    {
        return $this->attribute;
    }
}
