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

namespace App\Innoclapps\Settings\Stores;

class ArrayStore extends AbstractStore
{
    /**
     * Fire the post options to customize the store.
     *
     * @param array $options
     */
    protected function postOptions(array $options)
    {
        // Do nothing...
    }

    /**
     * Read the data from the store.
     *
     * @return array
     */
    protected function read() : array
    {
        return $this->data;
    }

    /**
     * Write the data into the store.
     *
     * @param array $data
     *
     * @return void
     */
    protected function write(array $data) : void
    {
        // Nothing to do...
    }
}
