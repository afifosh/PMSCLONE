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

namespace Modules\Contacts\Filters;

use Modules\Core\Filters\Country;
use Modules\Core\Filters\Operand;
use Modules\Core\Filters\OperandFilter;
use Modules\Core\Filters\Text;

class AddressOperandFilter extends OperandFilter
{
    /**
     * Initialize the AddressOperandFilter class
     *
     * @param  string  $resourceName
     */
    public function __construct($resourceName)
    {
        parent::__construct('address', __('core::app.address'));

        $this->setOperands([
            Operand::make('street', __('contacts::fields.'.$resourceName.'.street'))->filter(Text::class),
            Operand::make('city', __('contacts::fields.'.$resourceName.'.city'))->filter(Text::class),
            Operand::make('state', __('contacts::fields.'.$resourceName.'.state'))->filter(Text::class),
            Operand::make('postal_code', __('contacts::fields.'.$resourceName.'.postal_code'))->filter(Text::class),
            Operand::make('country_id', __('contacts::fields.'.$resourceName.'.country.name'))->filter(Country::make()),
        ]);
    }
}
