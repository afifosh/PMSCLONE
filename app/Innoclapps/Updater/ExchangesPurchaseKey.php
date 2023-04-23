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

namespace App\Innoclapps\Updater;

trait ExchangesPurchaseKey
{
    /**
     * @var string|null
     */
    protected ?string $purchaseKey = null;

    /**
     * Use the given custom purchase key
     *
     * @param string $key
     *
     * @return static
     */
    public function usePurchaseKey(string $key) : static
    {
        $this->purchaseKey = $key;

        return $this;
    }

    /**
     * Get the updater purchase key
     *
     * @return string|null
     */
    public function getPurchaseKey()
    {
        return $this->purchaseKey ?: $this->config['purchase_key'];
    }
}
