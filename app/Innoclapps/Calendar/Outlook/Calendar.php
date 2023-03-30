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

namespace App\Innoclapps\Calendar\Outlook;

use App\Innoclapps\Calendar\AbstractCalendar;

class Calendar extends AbstractCalendar
{
    /**
     * Get the calendar ID
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->getEntity()->getId();
    }

    /**
     * Get the calendar title
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->getEntity()->getName();
    }

    /**
    * Check whether the calendar is default
    *
    * @return string
    */
    public function isDefault() : bool
    {
        return $this->getEntity()->getIsDefaultCalendar();
    }
}