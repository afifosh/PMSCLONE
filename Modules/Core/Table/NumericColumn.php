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

use Akaunting\Money\Currency;

class NumericColumn extends Column
{
    /**
     * Indicates whether the field has currency
     */
    public null|Currency $currency = null;

    /**
     * Initialize new NumericColumn instance.
     */
    public function __construct(...$params)
    {
        parent::__construct(...$params);

        // Do not use queryAs as it's not supported (tested) for this type of column
        $this->displayAs(function ($model) {
            $value = $model->{$this->attribute};

            if (empty($value)) {
                return '--';
            }

            if (! $this->currency) {
                return $value;
            }

            return to_money($value, $this->currency)->format();
        });
    }

    /**
     * Set that the value should be displayed with currency
     */
    public function currency(string|null|Currency $currency = null): static
    {
        $this->currency = $currency;

        return $this;
    }
}
