<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Facades\OAuthState;
use Modules\Core\OAuth\OAuthManager;
use Modules\MailClient\Enums\EmailAccountType;

class OAuthEmailAccountController extends Controller
{
    /**
     * OAuth connect email account
     *
     * @param  string  $type shared|personal
     * @param  string  $providerName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connect(string $type, string $providerName, Request $request, OAuthManager $manager): RedirectResponse
    {
        abort_if(
            ! $request->user()->isSuperAdmin() && EmailAccountType::from($type) === EmailAccountType::SHARED,
            403,
            'Unauthorized action.'
        );

        return redirect($manager->createProvider($providerName)
            ->getAuthorizationUrl(['state' => $this->createState($request, $type, $manager)]));
    }

    /**
     * Create state
     */
    protected function createState(Request $request, string $type, OAuthManager $manager): string
    {
        return OAuthState::putWithParameters([
            'return_url' => '/mail/accounts?viaOAuth=true',
            'period' => $request->period,
            'email_account_type' => $type,
            're_auth' => $request->re_auth,
            'key' => $manager->generateRandomState(),
        ]);
    }
}
