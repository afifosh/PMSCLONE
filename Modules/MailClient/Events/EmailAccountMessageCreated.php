<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Events;

use Illuminate\Queue\SerializesModels;
use Modules\MailClient\Client\Contracts\MessageInterface;
use Modules\MailClient\Models\EmailAccountMessage;

class EmailAccountMessageCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public EmailAccountMessage $message, public MessageInterface $remoteMessage)
    {
    }
}
