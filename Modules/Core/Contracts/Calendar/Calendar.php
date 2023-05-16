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

namespace Modules\Core\Contracts\Calendar;

interface Calendar
{
    /**
     * Get the calendar ID.
     */
    public function getId(): int|string;

    /**
     * Get the calendar title.
     */
    public function getTitle(): string;

    /**
     * Check whether the calendar is default.
     */
    public function isDefault(): bool;
}
