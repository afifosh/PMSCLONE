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
use App\Innoclapps\MailClient\AbstractFolder;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;
use App\Innoclapps\MailClient\Exceptions\MessageNotFoundException;

class Folder extends AbstractFolder
{
    /**
     * Gmail Folder Delimiter
     */
    const DELIMITER = '/';

    /**
     * Get the folder unique identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->getEntity()->getId();
    }

    /**
     * Get folder message
     *
     * @param string $uid
     *
     * @return \App\Innoclapps\MailClient\Gmail\Message
     *
     * @throws \App\Innoclapps\MailClient\Exceptions\MessageNotFoundException
     * @throws \App\Innoclapps\MailClient\Exceptions\ConnectionErrorException
     */
    public function getMessage($uid)
    {
        try {
            $message = Client::message()
                ->withLabels($this->getId())
                ->get($uid);

            return new Message($message);
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() === 404) {
                throw new MessageNotFoundException($e->getMessage(), $e->getCode(), $e);
            } elseif ($e->getCode() == 401) {
                throw new ConnectionErrorException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }
    }

    /**
     * Get messages in the folder
     *
     * @param int $limit
     *
     * @return \Illuminate\Support\Collection&\App\Innoclapps\Google\Services\MessageCollection
     *
     * @throws \App\Innoclapps\MailClient\Exceptions\ConnectionErrorException
     */
    public function getMessages($limit = 50)
    {
        try {
            return Client::message()
                ->withLabels($this->getId())
                ->preload(Message::class)
                ->take($limit)
                ->all();
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() == 401) {
                throw new ConnectionErrorException;
            }

            throw $e;
        }
    }

    /**
     * Get messages starting from specific date and time
     *
     * @param string $dateTime
     * @param int $limit
     *
     * @return \Illuminate\Support\Collection&\App\Innoclapps\Google\Services\MessageCollection
     *
     * @throws \App\Innoclapps\MailClient\Exceptions\ConnectionErrorException
     */
    public function getMessagesFrom($dateTime, $limit = 50)
    {
        try {
            if(!is_null($dateTime)){
                $dateTime= $dateTime->format('Y-m-d H:i:s');
                return Client::message()
                ->withLabels($this->getId())
                ->preload(Message::class)
                ->after(strtotime($dateTime))
                ->take($limit)
                ->all();
            }
          else{
            return Client::message()
            ->withLabels($this->getId())
            ->preload(Message::class)
            ->take($limit)
            ->all();
          }
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() == 401) {
                throw new ConnectionErrorException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }
    }

    /**
     * Get the folder system name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getEntity()->getName();
    }

    /**
     * Get the folder display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return last(explode(self::DELIMITER, $this->getName()));
    }

    /**
     * Check whether the folder is selectable
     *
     * @return boolean
     */
    public function isSelectable()
    {
        return true;
    }

    /**
     * Check whether a message can be moved to this folder
     *
     * @return boolean
     */
    public function supportMove()
    {
        return ! $this->isDraft() && ! $this->isSent();
    }
}
