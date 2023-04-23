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

use App\Innoclapps\Facades\Format;

class DateTimeColumn extends Column
{
    /**
     * Initialize new DateTimeColumn instance.
     */
    public function __construct(?string $attribute = null, ?string $label = null)
    {
        parent::__construct($attribute, $label);

        $this->displayAs(fn ($model) => Format::dateTime($model->{$this->attribute}));
    }
}