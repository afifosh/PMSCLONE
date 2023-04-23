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

namespace App\Resources\Inbox\Filters;

use App\Innoclapps\Filters\Number;
use App\Innoclapps\Filters\HasMany;
use App\Innoclapps\Filters\Operand;

class ResourceEmailsFilter extends HasMany
{
    /**
     * Initialize ResourceEmailsFilter class
     */
    public function __construct()
    {
        parent::__construct('emails', __('mail.emails'));

        $this->setOperands([
            Operand::make('total_unread', __('inbox.unread_count'))->filter(
                Number::make('total_unread')->countableRelation('unreadEmailsForUser')
            ),
        ]);
    }
}
