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

use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;

class LengthAwarePaginator extends BaseLengthAwarePaginator
{
    protected array $merge = [];

    /**
     * Set the all time total
     */
    public function setAllTimeTotal(int $total) : static
    {
        return $this->merge(['all_time_total' => $total]);
    }

    /**
     * Add additional data to be merged with the response
     */
    public function merge(array $data) : static
    {
        $this->merge = array_merge($this->merge, $data);

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), $this->merge);
    }
}