<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\MailableTemplate;

class DefaultMailable
{
    /**
     * Create new default mail template
     *
     * @param  string  $message
     */
    public function __construct(protected string $html_message, protected string $subject, protected ?string $text_message = null)
    {
    }

    /**
     * Get the mailable default HTML message
     */
    public function htmlMessage(): string
    {
        return $this->html_message;
    }

    /**
     * Get the mailable default text message
     */
    public function textMessage(): ?string
    {
        return $this->text_message;
    }

    /**
     * Get the mailable default subject
     */
    public function subject(): string
    {
        return $this->subject;
    }
}
