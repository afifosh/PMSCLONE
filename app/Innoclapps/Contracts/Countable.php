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

namespace App\Innoclapps\Contracts;

interface Countable
{
    /**
     * Set that the class should count
     *
     * @return self
     */
    public function count() : static;

    /**
     * Check whether the class counts
     *
     * @return boolean
     */
    public function counts() : bool;

    /**
     * Get the count key
     *
     * @return string
     */
    public function countKey() : string;
}
