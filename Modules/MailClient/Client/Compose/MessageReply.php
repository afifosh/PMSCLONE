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

namespace Modules\MailClient\Client\Compose;

use Modules\MailClient\Client\Client;
use Modules\MailClient\Client\FolderIdentifier;

class MessageReply extends AbstractComposer
{
    /**
     * Holds the message ID the reply is intended for
     */
    protected string|int $id;

    /**
     * Holds the folder the message belongs to
     */
    protected FolderIdentifier $folder;

    /**
     * Create new MessageReply instance
     */
    public function __construct(Client $client, string|int $remoteId, FolderIdentifier $folder, ?FolderIdentifier $sentFolder = null)
    {
        parent::__construct($client, $sentFolder);

        $this->id = $remoteId;
        $this->folder = $folder;
    }

    /**
     * Reply to the message
     *
     * @return \Modules\MailClient\Client\Contracts\MessageInterface
     */
    public function send()
    {
        return $this->client->reply($this->id, $this->folder);
    }
}
