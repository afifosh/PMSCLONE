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

namespace App\Innoclapps\Calendar;

use App\Innoclapps\AbstractMask;
use App\Innoclapps\Contracts\Calendar\Calendar as CalendarInterface;

abstract class AbstractCalendar extends AbstractMask implements CalendarInterface
{
    /**
     * Serialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'         => $this->getId(),
            'title'      => $this->getTitle(),
            'is_default' => $this->isDefault(),
        ];
    }
}
