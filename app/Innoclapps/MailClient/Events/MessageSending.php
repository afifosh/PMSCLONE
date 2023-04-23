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

namespace App\Innoclapps\MailClient\Events;

use App\Innoclapps\MailClient\Client;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSending
{
    use Dispatchable;

    /**
     * Create new MessageSending instance
     *
     * @param \App\Innoclapps\MailClient\Client $client
     */
    public function __construct(public Client $client)
    {
    }
}
