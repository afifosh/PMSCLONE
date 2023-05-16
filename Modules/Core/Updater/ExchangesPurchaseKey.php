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

namespace Modules\Core\Updater;

trait ExchangesPurchaseKey
{
    protected ?string $purchaseKey = null;

    /**
     * Use the given custom purchase key.
     */
    public function usePurchaseKey(string $key): static
    {
        $this->purchaseKey = $key;

        return $this;
    }

    /**
     * Get the updater purchase key.
     */
    public function getPurchaseKey(): ?string
    {
        return $this->purchaseKey ?: $this->config['purchase_key'];
    }
}
