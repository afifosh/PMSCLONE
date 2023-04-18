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

namespace App\Innoclapps\Google\Services;

use Google_Service_Gmail;
use Illuminate\Support\Collection;
use App\Innoclapps\Google\Services\Message\Mail;
use App\Innoclapps\Google\Services\Message\SendMail;
use Google_Service_Gmail_BatchDeleteMessagesRequest;
use Google_Service_Gmail_BatchModifyMessagesRequest;

class Message extends Service
{
    /**
     * Indicates whether to preload the messages and it's attachments
     *
     * @var boolean|string
     */
    protected bool|string $preload = false;

    /**
     * Request params
     *
     * @var array
     */
    protected array $params = [];

    /**
     * @link https://developers.google.com/gmail/api/guides/batch
     *
     * @var integer
     */
    protected int $batchSize = 50;

    /**
     * Message constructor.
     *
     * @param \Google_Client $client
     */
    public function __construct($client)
    {
        parent::__construct($client, Google_Service_Gmail::class);
    }

    /**
     * Create new SendMail instance.
     *
     * @return \App\Innoclapps\Google\Services\Message\SendMail
     */
    public function sendMail() : SendMail
    {
        return new SendMail($this->client);
    }

    /**
     * Preload the messages or preload them mapped in a given class
     *
     * @param boolean|string $state
     *
     * @return static
     */
    public function preload(bool|string $state = true)
    {
        $this->preload = $state;

        return $this;
    }

    /**
     * Retrieve messages
     *
     * @param string|null $pageToken
     *
     * @return \App\Innoclapps\Google\Services\MessageCollection
     */
    public function all($pageToken = null)
    {
        if (! is_null($pageToken)) {
            $this->params['pageToken'] = $pageToken;
        }

        /**
         * List of messages. Note that each message resource contains only an id and a threadId.
         * Additional message details can be fetched using the messages.get method.
         */
        $response = $this->service->users_messages->listUsersMessages('me', $this->params);
        $messages = (new MessageCollection)->setMessageService($this)
                    ->setNextPageToken($response->getNextPageToken())
                    ->setResultSizeEstimate($response->getResultSizeEstimate());

        // If preload, create a batch requests for the messages
        if ($this->preload) {
            return $messages->concat($this->batchRequest($response->getMessages()))
                    ->when(is_string($this->preload), fn ($collection) => $collection->mapInto($this->preload));
        }

        return $messages->concat($response->getMessages())
                ->map(fn ($message) => new Mail($this->client, $message))
                ->when(is_string($this->preload), fn ($collection) => $collection->mapInto($this->preload));

        /**
         * If preloaded, load the attachments too
         * UPDATE: Do not preload, takes too much memory
         */

        // if ($this->preload) {
        //     $this->loadAttachments($messages);
        // }
    }

    /**
     * Get message by id
     *
     * @param string $id
     *
     * @return \App\Innoclapps\Google\Services\Message\Mail
     */
    public function get($id) : Mail
    {
        /**
         * @var \Google_Service_Gmail_Message
         */
        $message = $this->service->users_messages->get('me', $id);

        return new Mail($this->client, $message);
    }

    /**
     * Creates a batch request to get all emails in a single call
     *
     * @param array|\Illuminate\Support\Collection $messages
     *
     * @return \Illuminate\Support\Collection
     */
    public function batchRequest($messages)
    {
        $this->client->setUseBatch(true);

        $result = new Collection;

        if (! $messages instanceof Collection) {
            $messages = new Collection($messages);
        }

        $messages->chunk($this->batchSize)->each(function ($messages) use (&$result) {
            $batch = $this->service->createBatch();

            foreach ($messages as $key => $message) {
                $batch->add($this->service->users_messages->get('me', $message->getId()), $key + 1);
            }

            $result = $result->when($batch->execute(), function ($collection, $messages) {
                (new Collection($messages))->reject(
                    fn ($message) => $message instanceof \Google_Service_Exception
                )
                ->each(function ($message) use (&$collection) {
                    $collection->push(new Mail($this->client, $message));
                });

                return $collection;
            });

            $batch = null;
        });

        $this->client->setUseBatch(false);

        return $result;
    }

    /**
     * Batch move messages to a given folder
     *
     * @param array $messages
     * @param array $removeLabelIds
     * @param array $addLabelIds
     *
     * @return boolean
     */
    public function batchModify($messages, $removeLabelIds = [], $addLabelIds = [])
    {
        $request = new Google_Service_Gmail_BatchModifyMessagesRequest();

        $request->setRemoveLabelIds($removeLabelIds);
        $request->setAddLabelIds($addLabelIds);
        $request->setIds($messages);

        $this->service->users_messages->batchModify('me', $request);

        return true;
    }

    /**
     * Batch delete messages
     *
     * @param array $messages
     *
     * @return void
     */
    public function batchDelete($messages)
    {
        $request = new Google_Service_Gmail_BatchDeleteMessagesRequest();

        $request->setIds($messages);

        $this->service->users_messages->batchDelete('me', $request);
    }

    /**
     * Limit the messages coming from the query
     *
     * @param int $number
     *
     * @return static
     */
    public function take($number)
    {
        $this->params['maxResults'] = abs((int) $number);

        return $this;
    }

    /**
     * Whether to include the spam and the trash messages
     *
     * @param boolean $boolean
     *
     * @return static
     */
    public function includeSpamTrash($boolean = true)
    {
        $this->params['includeSpamTrash'] = $boolean;

        return $this;
    }

    /**
     * Filter messages only with specific label ids
     *
     * @param string|array $ids
     *
     * @return static
     */
    public function withLabels($ids)
    {
        $this->params['labelIds'] = (array) $ids;

        return $this;
    }

    /**
     * Filter messages by subject
     *
     * @param string $query
     *
     * @return static
     */
    public function subject(string $query)
    {
        $this->addQuery("[{$query}]");

        return $this;
    }

    /**
     * Filter to get only emails to a specific email address
     *
     * @param string $email
     *
     * @return static
     */
    public function to(string $email)
    {
        $this->addQuery("to:{$email}");

        return $this;
    }

    /**
     * Filter to get only emails from a specific email address
     *
     * @param $email
     *
     * @return static
     */
    public function from(string $email)
    {
        $this->addQuery("from:{$email}");

        return $this;
    }

    /**
     * Filter to get only emails after a specific date
     *
     * @param $date
     *
     * @return static
     */
    public function after(string $date)
    {
        $this->addQuery("after:{$date}");

        return $this;
    }

    /**
     * Filter to get only emails before a specific date
     *
     * @param $date
     *
     * @return static
     */
    public function before(string $date)
    {
        $this->addQuery("before:{$date}");

        return $this;
    }

    /**
     * Filters emails by label
     *
     * Example:
     * starred, inbox, spam, chats, sent, draft, trash
     *
     * @param string $box
     *
     * @return static
     */
    public function in(string $box)
    {
        $this->addQuery("in:{$box}");

        return $this;
    }

    /**
     * Filter emails with attachment
     *
     * @return static
     */
    public function hasAttachment()
    {
        $this->addQuery('has:attachment');

        return $this;
    }

    /**
     * Eager load the messages attachments
     *
     * @param \Illuminate\Support\Collection $messages
     *
     * @return static
     */
    public function loadAttachments(&$messages)
    {
        $attachments = $this->prepareAttachmentsForBatch($messages);

        $this->client->setUseBatch(true);

        $attachments->chunk($this->batchSize)->each(function ($attachments) use (&$messages) {
            $batch = $this->service->createBatch();

            foreach ($attachments as $data) {
                $batch->add(
                    $this->service->users_messages_attachments->get('me', $data->message_id, $data->attachment_id),
                    $data->attachment_key . '-' . $data->message_key
                );
            }

            $response = $batch->execute();

            $this->handleAttachmentsBatchResponse($response, $messages);

            $response = null;
            $batch = null;
        });

        $this->client->setUseBatch(false);

        return $this;
    }

    /**
     * Prepare the attachments data for batch request
     *
     * @param \Illuminate\Support\Collection $messages
     *
     * @return \Illuminate\Support\Collection
     */
    protected function prepareAttachmentsForBatch($messages)
    {
        $attachments = new Collection;

        foreach ($messages as $messageKey => $message) {
            foreach ($message->getAttachments() as $attachmentKey => $attachment) {
                $attachments->push((object) [
                    'attachment_id'  => $attachment->getId(),
                    'message_id'     => $message->getId(),
                    'message_key'    => $messageKey,
                    'attachment_key' => $attachmentKey,
                ]);
            }
        }

        return $attachments;
    }

    /**
     * Handles the attachments batch response
     *
     * @param array $response
     * @param \Illuminate\Support\Collection $messages
     *
     * @return void
     */
    protected function handleAttachmentsBatchResponse($response, &$messages)
    {
        foreach ($response as $key => $attachment) {
            // e.q. response-0-2
            $keyPieces = explode('-', $key);

            $attachmentKey = $keyPieces[1];
            $messageKey    = $keyPieces[2];

            $messages->get($messageKey)
                ->getAttachments()
                ->get($attachmentKey)
                ->setContent($attachment->getData());
        }
    }

    /**
     * Add query to params
     *
     * @param string $query
     */
    protected function addQuery($query)
    {
        if (isset($this->params['q'])) {
            $this->params['q'] .= ' ' . $query;
        } else {
            $this->params['q'] = $query;
        }

        return $this;
    }
}
