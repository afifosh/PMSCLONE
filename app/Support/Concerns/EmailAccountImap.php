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

namespace App\Support\Concerns;

use App\Innoclapps\MailClient\Imap\SmtpConfig;
use App\Innoclapps\MailClient\Imap\Config as ImapConfig;

trait EmailAccountImap
{
    /**
     * Get the Imap client configuration
     *
     * @return \App\Innoclapps\MailClient\Imap\Config
     */
    public function getImapConfig() : ImapConfig
    {
        return new ImapConfig(
            $this->imap_server,
            $this->imap_port,
            $this->imap_encryption,
            $this->email,
            $this->validate_cert,
            $this->username,
            $this->password
        );
    }

    /**
     * Get the Smtp client configuration
     *
     * @return \App\Innoclapps\MailClient\Imap\SmtpConfig
     */
    public function getSmtpConfig() : SmtpConfig
    {
        return new SmtpConfig(
            $this->smtp_server,
            $this->smtp_port,
            $this->smtp_encryption,
            $this->email,
            $this->validate_cert,
            $this->username,
            $this->password
        );
    }
}
