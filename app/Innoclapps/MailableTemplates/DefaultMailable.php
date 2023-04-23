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

namespace App\Innoclapps\MailableTemplates;

class DefaultMailable
{
    /**
     * Create new default mail template
     *
     * @param string $message
     * @param string $html_message
     * @param string $subject
     * @param string|null $text_message
     */
    public function __construct(protected string $html_message, protected string $subject, protected ?string $text_message = null)
    {
    }

    /**
     * Get the mailable default HTML message
     *
     * @return string
     */
    public function htmlMessage() : string
    {
        return $this->html_message;
    }

    /**
     * Get the mailable default text message
     *
     * @return string|null
     */
    public function textMessage() : ?string
    {
        return $this->text_message;
    }

    /**
     * Get the mailable default subject
     *
     * @return string
     */
    public function subject() : string
    {
        return $this->subject;
    }
}
