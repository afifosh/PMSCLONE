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

use Google_Service_Gmail;
use Google_Service_Gmail_MessagePart;
use App\Innoclapps\Google\Concerns\HasHeaders;
use App\Innoclapps\Mail\Headers\HeadersCollection;
use App\Innoclapps\Google\Concerns\HasDecodeableBody;

class Attachment
{
    use HasDecodeableBody,
        HasHeaders;

    /**
     * Holds the actual Gmail attachment part
     *
     * @var \Google_Service_Gmail_MessagePart
     */
    protected $part;

    /**
     * Holds the Gmail service
     *
     * @var \Google_Service_Gmail
     */
    protected $service;

    /**
     * The message id the attachment is linked to
     *
     * @var string
     */
    protected $messageId;

    /**
     * Attachment constructor.
     *
     * @param string $messageId
     * @param \Google_Service_Gmail_MessagePart $part
     */
    public function __construct($client, $messageId, Google_Service_Gmail_MessagePart $part)
    {
        $this->client    = $client;
        $this->part      = $part;
        $this->messageId = $messageId;
        $this->service   = new Google_Service_Gmail($client);

        $this->headers = new HeadersCollection;

        foreach ($part->getHeaders() as $header) {
            $this->headers->pushHeader($header->getName(), $header->getValue());
        }
    }

    /**
     * Retuns attachment ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->part->getBody()->getAttachmentId();
    }

    /**
     * Get the attachment content id
     *
     * Available only for inline attachments with CID (Content-ID)
     *
     * @return string|null
     */
    public function getContentId()
    {
        $contentId = $this->getHeaderValue('content-id');

        if (! $contentId) {
            $contentId = $this->getHeaderValue('x-attachment-id');
        }

        return ! is_null($contentId) ? str_replace(['<', '>'], '', $contentId) : null;
    }

    /**
     * Returns attachment file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->part->getFilename();
    }

    /**
     * Returns mime type of the attachment
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->part->getMimeType();
    }

    /**
     * Checks whether the attachments is inline
     *
     * @return boolean
     */
    public function isInline()
    {
        if ($this->getHeaderValue('content-id') || $this->getHeaderValue('x-attachment-id')) {
            return true;
        }

        return str_contains($this->getHeaderValue('content-disposition'), 'inline');
    }

    /**
     * Get the attachment encoding
     *
     * @return string|null
     */
    public function getEncoding()
    {
        return $this->getHeaderValue('content-transfer-encoding');
    }

    /**
     * Returns approximate size of the attachment
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->part->getBody()->getSize();
    }

    /**
     * Get the attachment content
     *
     * @return string
     */
    public function getContent()
    {
        $attachment = $this->retrieve();

        return  $this->getDecodedBody($attachment->getData());
    }

    /**
     * Retrieve the attachment from Gmail API
     *
     * @return \Google_Service_Gmail_MessagePartBody
     */
    protected function retrieve()
    {
        return $this->service->users_messages_attachments->get(
            'me',
            $this->messageId,
            $this->getId()
        );
    }
}
