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

namespace App\Innoclapps\MailClient\Gmail;

use Google_Service_Exception;
use App\Innoclapps\Facades\Google as Client;
use App\Innoclapps\OAuth\AccessTokenProvider;
use App\Innoclapps\MailClient\FolderCollection;
use App\Innoclapps\MailClient\FolderIdentifier;
use App\Innoclapps\MailClient\AbstractImapClient;
use App\Innoclapps\Contracts\MailClient\FolderInterface;
use App\Innoclapps\Contracts\MailClient\MessageInterface;
use App\Innoclapps\MailClient\Exceptions\UnauthorizedException;
use App\Innoclapps\MailClient\Exceptions\FolderNotFoundException;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;
use App\Innoclapps\MailClient\Exceptions\MessageNotFoundException;

class ImapClient extends AbstractImapClient
{
    /**
     * Ignore folders by id
     *
     * @var array
     */
    protected $ignoredFoldersById = [
        'UNREAD',
        'CHAT',
        'STARRED',
        'PERSONAL', // Personal label is not even shown in Gmail
    ];

    /**
     * Create new ImapClient instance.
     *
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     */
    public function __construct(protected AccessTokenProvider $token)
    {
        Client::connectUsing($token);
    }

    /**
     * Get folder by a given id
     *
     * @param string $id The folder identifier
     *
     * @return \App\Innoclapps\MailClient\Outlook\Folder
     */
    public function getFolder($id)
    {
        try {
            return $this->exceptionHandler(
                fn () => $this->maskFolder(Client::labels()->get($id))
            );
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() === 404) {
                throw new FolderNotFoundException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }
    }

    /**
     * Retrieve the account available folders from remote server
     *
     * @return \App\Innoclapps\MailClient\FolderCollection
     */
    public function retrieveFolders()
    {
        return $this->exceptionHandler(function () {
            return $this->maskFolders(Client::labels()->list())->createTreeFromDelimiter(Folder::DELIMITER);
        });
    }

    /**
     * Get message by message identifier
     *
     * @param string $id
     * @param \App\Innoclapps\MailClient\FolderIdentifier $folder
     *
     * @return \App\Innoclapps\MailClient\Gmail\Message
     */
    public function getMessage($id, ?FolderIdentifier $folder = null)
    {
        try {
            return $this->exceptionHandler(fn () => new Message(Client::message()->get($id)));
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() === 404) {
                throw new MessageNotFoundException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }
    }

    /**
     * Get all account messages
     *
     * @param int $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMessages($limit = 50)
    {
        return $this->exceptionHandler(function () use ($limit) {
            return Client::message()->take($limit)
                    ->preload(Message::class)
                    ->includeSpamTrash()
                    ->all();
        });
    }

    /**
     * Move a message to a given folder
     *
     * @todo  TEST THIS METHOD
     *
     * @param \App\Innoclapps\Contracts\MailClient\FolderInterface $folder
     *
     * @return boolean
     */
    public function moveMessage(MessageInterface $message, FolderInterface $folder)
    {
        return $this->exceptionHandler(
            fn () => (bool) $this->getMessage($message->getId())->addLabel($folder->getName())
        );
    }

    /**
     * Batch move messages to a given folder
     *
     * @param array $messages
     * @param \App\Innoclapps\Contracts\MailClient\FolderInterface $from
     * @param \App\Innoclapps\Contracts\MailClient\FolderInterface $to
     *
     * @return boolean
     */
    public function batchMoveMessages($messages, FolderInterface $to, FolderInterface $from)
    {
        return $this->exceptionHandler(function () use ($messages, $to, $from) {
            // Gmail doesn't allow removing the "SENT" or "DRAFT" label, in this case
            // we don't pass any label to remove, only pass to add the label and Gmail will do it's job
            $removeLabels = $from->supportMove() ? [$from->getId()] : [];
            $addLabels = [$to->getId()];

            return Client::message()->batchModify($messages, $removeLabels, $addLabels);
        });
    }

    /**
    * Permanently batch delete messages
    *
    * @param array $messages
    *
    * @return void
    */
    public function batchDeleteMessages($messages)
    {
        $this->exceptionHandler(function () use ($messages) {
            Client::message()->batchDelete($messages);
        });
    }

    /**
     * Batch mark as read messages
     *
     * @param array $messages
     * @param \App\Innoclapps\MailClient\FolderIdentifier $folder
     *
     * @return boolean
     */
    public function batchMarkAsRead($messages, ?FolderIdentifier $folder = null)
    {
        return $this->exceptionHandler(fn () => Client::message()->batchModify($messages, ['UNREAD']));
    }

    /**
     * Batch mark as unread messages
     *
     * @param array $messages
     * @param \App\Innoclapps\MailClient\FolderIdentifier $folder
     *
     * @return boolean
     */
    public function batchMarkAsUnread($messages, ?FolderIdentifier $folder = null)
    {
        return $this->exceptionHandler(fn () => Client::message()->batchModify($messages, [], ['UNREAD']));
    }

    /**
     * Batch get messages
     *
     * @param array|\Illuminate\Support\Collection $messages
     *
     * @return \Illuminate\Support\Collection
     */
    public function batchGetMessages($messages)
    {
        return $this->exceptionHandler(function () use ($messages) {
            return Client::message()
                    ->batchRequest($messages)
                    ->mapInto(Message::class);
        });
    }

    /**
    * Get the latest message from the sent folder
    *
    * @return \App\Innoclapps\MailClient\Gmail\Message|null
    */
    public function getLatestSentMessage()
    {
        return $this->exceptionHandler(function () {
            $messages = Client::message()->take(1)->in('sent')->preload()->all();

            if ($message = $messages->first()) {
                return new Message($message);
            }

            return null;
        });
    }

    /**
     * Get mailbox history
     *
     * https://developers.google.com/gmail/api/v1/reference/users/history/list
     *
     * @param int $historyId
     * @param array $optParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHistory($historyId, $optParams = [])
    {
        return $this->exceptionHandler(function () use ($historyId, $optParams) {
            $params = array_merge(['startHistoryId' => intval($historyId)], $optParams);

            return Client::history()->get($params);
        });
    }

    /**
     * Common exceptions handler
     *
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function exceptionHandler($closure)
    {
        try {
            return $closure();
        } catch (Google_Service_Exception $e) {
            $message = $e->getErrors()[0]['message'] ?? $e->getMessage();
            if ($e->getCode() == 401) {
                throw new ConnectionErrorException($message, $e->getCode(), $e);
            } elseif ($e->getCode() == 403) {
                throw new UnauthorizedException($message, $e->getCode(), $e);
            }

            throw $e;
        }
    }

    /**
     * Mask folders
     *
     * @param array $folders
     *
     * @return \App\Innoclapps\MailClient\FolderCollection
     */
    protected function maskFolders($folders)
    {
        return (new FolderCollection($folders))->map(function ($folder) {
            return $this->maskFolder($folder);
        })->reject(function ($folder) {
            // Email account draft folders are not supported
            return in_array($folder->getId(), $this->ignoredFoldersById) || $folder->isDraft();
        })->values();
    }

    /**
     * Mask folder
     *
     * @param mixed $folder
     *
     * @return \App\Innoclapps\MailClient\Gmail\Folder
     */
    protected function maskFolder($folder)
    {
        return new Folder($folder);
    }
}
