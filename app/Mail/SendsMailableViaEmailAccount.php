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

namespace App\Mail;

use App\Innoclapps\MailClient\SendsMailForMailable;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;

trait SendsMailableViaEmailAccount
{
    use SendsMailForMailable;

    /**
     * Provide the email account id
     */
    abstract protected function emailAccountId() : ?int;

    /**
     * Get the client instance that should be used to send the mailable
     *
     * @return \App\Innoclapps\MailClient\Client|null
     */
    protected function getClient()
    {
        if (! $accountId = $this->emailAccountId()) {
            return;
        }

        $repository = app(EmailAccountRepository::class);
        $account    = $repository->find($accountId);

        // We will check if the email account requires authentication, as we
        // are not able to send mails if the account requires authentication
        // the template will fallback to the Laravel default mailer behavior
        if (! $account->canSendMails()) {
            return;
        }

        $client = $account->getClient();

        if ($fromName = $this->accountFromName()) {
            $client->setFromName($fromName);
        }

        return $client;
    }

    /**
     * Get custom account from name text
     *
     * @return string|null
     */
    protected function accountFromName() : ?string
    {
        return null;
    }

    /**
     * Handle connection error exception
     *
     * @param App\Innoclapps\MailClient\Exceptions\ConnectionErrorException $e
     *
     * @return void
     */
    protected function onConnectionError(ConnectionErrorException $e) : void
    {
        app(EmailAccountRepository::class)->setRequiresAuthentication(
            $this->emailAccountId()
        );
    }
}