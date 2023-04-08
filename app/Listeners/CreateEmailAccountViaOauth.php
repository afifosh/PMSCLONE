<?php

namespace App\Listeners;

use App\Models\EmailAccount;
use Illuminate\Http\Request;
use App\Enums\EmailAccountType;
use App\Innoclapps\Facades\OAuthState;
use Illuminate\Support\Facades\Session;
use App\Innoclapps\MailClient\ClientManager;
use App\Innoclapps\MailClient\ConnectionType;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\MailClient\Exceptions\UnauthorizedException;
use App\Models\EmailAccountMessage;

class CreateEmailAccountViaOAuth
{
    /**
     * Initialize new CreateEmailAccountViaOAuth instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(protected EmailAccountRepository $repository, protected Request $request)
    {
    }

    /**
     * Handle Microsoft email account connection finished
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $oAuthAccount = $event->account;
        $account      = $this->repository->findByEmail($oAuthAccount->email);

        // Connection not intended for email account
        // Connection can be invoke via /oauth/accounts route or calendar because of re-authentication
        $emailAccountBeingConnected = ! is_null(OAuthState::getParameter('email_account_type'));

        if (! $emailAccountBeingConnected) {
            // We will check if this OAuth account actually exists and if yes,
            // we will make sure that the account is usable and it does not require authentication in database
            // as well that sync is enabled in case stopped previously e.q. because of refresh token
            // in this case, the user won't need to re-authenticate via the email accounts index area again
            if ($account) {
                $this->makeSureAccountIsUsable($account);
            }

            return;
        }

        if (! $account) {
            if (! $account = $this->createEmailAccount($oAuthAccount)) {
                return;
            }
        } elseif ((string) OAuthState::getParameter('re_auth') !== '1') {
            Session::flash('warning', __('mail.account.already_connected'));
        }

        $this->makeSureAccountIsUsable($account);

        // Update the access_token_id because it's not set in the createEmailAccount method
        EmailAccount::unguarded(function () use ($account, $oAuthAccount) {
            $this->repository->update(['access_token_id' => $oAuthAccount->id], $account->id);
        });
    }

    /**
     * Make sure that the account is usable
     * Sets requires autentication to false as well enabled sync again if is stopped by system
     *
     * @param \App\Models\EmailAccount $account
     *
     * @return void
     */
    protected function makeSureAccountIsUsable($account)
    {
        $this->repository->setRequiresAuthentication((int) $account->id, false);

        // If the sync is stopped, probably it's because of empty refresh token or
        // failed authenticated for some reason, when reconnected, enable sync again
        if ($account->isSyncStoppedBySystem()) {
            $this->repository->enableSync((int) $account->id);
        }
    }

    /**
     * Create the email account
     *
     * @param \App\Innoclapps\Models\OAuthAccount $oAuthAccount
     *
     * @return \App\Models\EmailAccount
     */
    protected function createEmailAccount($oAuthAccount)
    {
        $accounts=EmailAccount::where("email",'=',$oAuthAccount->email)->withTrashed();
        if($accounts->count()>0){
           $account=$accounts->first();
            EmailAccountMessage::where(["email_account_id"=>$account->id])->withTrashed()->forceDelete();
            $account->forceDelete();
        }
        

        $payload = [
            'connection_type' => $oAuthAccount->type == 'microsoft' ?
                ConnectionType::Outlook :
                ConnectionType::Gmail,
            'email' => $oAuthAccount->email,
        ];
        
        // When initially connected an account e.q. Gmail, we will try to retrieve the folders
        // however, if the user did not enabled the Gmail API, will throw an error, catching the exception
        // below will make sure that the user will actually see an error message so he can take steps

        try {
            $folders = ClientManager::createClient(
                $payload['connection_type'],
                $oAuthAccount->tokenProvider()
            )
            ->getImap()
            ->getFolders();

            $payload['folders'] = $folders->toArray();
        } catch (UnauthorizedException $e) {
            Session::flash('warning', $e->getMessage());

            return;
        }

        $payload['initial_sync_from'] = OAuthState::getParameter('period');

        if ($this->isPersonal()) {
            $payload['user_id'] = $this->request->user()->id;
        }

        return $this->repository->create($payload);
    }

    /**
     * Check whether the account is personal
     *
     * @return boolean
     */
    protected function isPersonal() : bool
    {
        return EmailAccountType::tryFrom(
            OAuthState::getParameter('email_account_type')
        ) === EmailAccountType::PERSONAL;
    }
}
