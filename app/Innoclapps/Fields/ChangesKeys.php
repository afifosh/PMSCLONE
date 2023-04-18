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

namespace App\Innoclapps\Fields;

trait ChangesKeys
{
    /**
     * From where the value key should be taken
     * @var string
     */
    public string $valueKey = 'value';

    /**
     * From where the label key should be taken
     * @var string
     */
    public string $labelKey = 'label';

    /**
     * Set custom key for value
     *
     * @param string $key
     *
     * @return mixed
     */
    public function valueKey(string $key) : static
    {
        $this->valueKey = $key;

        return $this;
    }

    /**
     * Set custom label key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function labelKey(string $key) : static
    {
        $this->labelKey = $key;

        return $this;
    }
}
