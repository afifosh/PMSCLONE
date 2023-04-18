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

namespace App\Innoclapps\Google\Services\Message;

use Exception;
use Google_Service_Gmail;
use Illuminate\Support\Carbon;
use Google_Service_Gmail_Message;
use Illuminate\Support\Collection;
use App\Innoclapps\Google\Concerns\HasParts;
use App\Innoclapps\Google\Concerns\HasHeaders;
use App\Innoclapps\Mail\Headers\HeadersCollection;
use App\Innoclapps\Google\Concerns\HasDecodeableBody;

class Mail
{
    use HasParts,
        HasHeaders,
        ModifiesMail,
        HasDecodeableBody,
        ProvidesMailAttachments;

    /**
     * The message request payload
     *
     * @var \Google_Service_Gmail_MessagePart
     */
    protected $payload;

    /**
     * Hold the messages parts
     *
     * @var \Illuminate\Support\Collection
     */
    protected $parts;

    /**
     * Hold the Google Gmail Service
     *
     * @var \Google_Service_Gmail
     */
    protected $service;

    /**
     * SingleMessage constructor.
     *
     * @param \Google_Client $client
     * @param \Google_Service_Gmail_Message $message
     */
    public function __construct(protected $client, protected Google_Service_Gmail_Message $message)
    {
        $this->service = new Google_Service_Gmail($client);

        // If payload is empty, the message is not preloaded
        // Use $message->load() to load the message data
        if ($payload = $message->getPayload()) {
            $this->payload = $payload;

            $this->headers = new HeadersCollection;

            foreach ($payload->getHeaders() as $header) {
                $this->headers->pushHeader($header->getName(), $header->getValue());
            }

            $this->parts = new Collection($payload->getParts());
        }
    }

    /**
     * Load message
     *
     * @return static
     */
    public function load()
    {
        $message = $this->service->users_messages->get('me', $this->getId());

        return new self($this->client, $message);
    }

    /**
     * Returns Gmail ID of the message
     *
     * * Available when the message is not loaded
     *
     * @return string
     */
    public function getId()
    {
        return $this->message->getId();
    }

    /**
     * Get the message Internet ID
     *
     * @return string
     */
    public function getInternetMessageId()
    {
        return $this->getHeaderValue('Message-ID');
    }

    /**
     * Get the message references
     *
     * @return array|null
     */
    public function getReferences()
    {
        $header = $this->getHeaders()->find('References');

        return $header ? $header->getIds() : null;
    }

    /**
     * https://developers.google.com/gmail/api/guides/sync
     *
     * Get the message history id
     *
     * @return int
     */
    public function getHistoryId()
    {
        return $this->message->getHistoryId();
    }

    /**
     * Return a UNIX version of the date
     *
     * @return int UNIX date
     */
    public function getInternalDate()
    {
        return $this->message->getInternalDate();
    }

    /**
     * Returns the labels of the email
     * Example: [INBOX, STARRED, UNREAD]
     *
     * @return array|null
     */
    public function getLabels()
    {
        return $this->message->getLabelIds();
    }

    /**
     * Returns approximate size of the email
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->message->getSizeEstimate();
    }

    /**
     * Returns thread ID of the email
     *
     * Available when the message is not loaded
     *
     * @return string
     */
    public function getThreadId()
    {
        return $this->message->getThreadId();
    }

    /**
     * Returns the subject of the email
     *
     * @return string|null
     */
    public function getSubject()
    {
        return $this->getHeaderValue('subject');
    }

    /**
     * Returns array of name and email of each recipient
     *
     * @return \App\Innoclapps\Mail\Headers\AddressHeader
     */
    public function getFrom()
    {
        return $this->getHeader('from');
    }

    /**
     * Returns the subject of the email
     *
     * @return \App\Innoclapps\Mail\Headers\AddressHeader
     */
    public function getReplyTo()
    {
        return $this->getHeader('reply-to') ?? $this->getFrom();
    }

    /**
     * Returns array of name and email of each recipient
     *
     * @return \App\Innoclapps\Mail\Headers\AddressHeader|null
     */
    public function getCC()
    {
        return $this->getHeader('cc');
    }

    /**
     * Returns array of name and email of each recipient
     *
     * @return \App\Innoclapps\Mail\Headers\AddressHeader|null
     */
    public function getBcc()
    {
        return $this->getHeader('bcc');
    }

    /**
     * Returns array list of recipients
     *
     * @return \App\Innoclapps\Mail\Headers\AddressHeader|null
     */
    public function getTo()
    {
        return $this->getHeader('to');
    }

    /**
     * Returns the original date that the email was sent
     *
     * @return \Illuminate\Support\Carbon
     */
    public function getDate()
    {
        return Carbon::parse($this->getHeaderValue('date'));
    }

    /**
     * Get the raw HTML body
     *
     * @return string
     */
    public function getRawHtmlBody()
    {
        return $this->getHtmlBody(true);
    }

    /**
     * Gets the HTML body
     *
     * @param bool $raw
     *
     * @return string|null
     */
    public function getHtmlBody($raw = false)
    {
        $content = $this->getBody('text/html');

        if ($raw) {
            return $content;
        }

        if (is_string($content)) {
            $body = trim(base64_decode($this->getDecodedBody($content)));

            return empty($body) ? null : $body;
        }

        return null;
    }

    /**
     * Get the base64 version of the body
     *
     * @return string|null
     */
    public function getRawPlainTextBody()
    {
        return $this->getPlainTextBody(true);
    }

    /**
     * Get the plain text body
     *
     * @param boolean $raw
     *
     * @return string|null
     */
    public function getPlainTextBody($raw = false)
    {
        $content = $this->getBody();

        if ($raw) {
            return $content;
        }

        if (is_string($content)) {
            $body = trim(base64_decode($this->getDecodedBody($content)));

            return empty($body) ? null : $body;
        }

        return null;
    }

    /**
     * Returns a specific body part from an email
     *
     * @param string $type
     *
     * @return null|string
     *
     * @throws \Exception
     */
    public function getBody($type = 'text/plain')
    {
        $parts = $this->getAllParts($this->parts);

        if ($this->payload->mimeType == $type && $parts->isEmpty()) {
            return $this->payload->body->data;
        }

        foreach ($parts as $part) {
            if ($part->mimeType == $type) {
                return $part->body->data;
            }
        }

        return null;
    }

    /**
     * Checks if message has at least one part without iterating through all parts
     *
     * @return boolean
     */
    public function hasParts()
    {
        return ! ! $this->iterateParts($this->parts, true);
    }

    /**
     * Initialize new MailReply instance.
     *
     * @return \App\Innoclapps\Google\Services\Message\MailReply
     */
    public function reply() : MailReply
    {
        if (! $this->payload) {
            throw new Exception('Message not loaded.');
        }

        return new MailReply($this->client, $this);
    }
}
