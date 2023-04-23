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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use App\Innoclapps\Facades\OAuthState;
use App\Innoclapps\OAuth\OAuthManager;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuthController extends Controller
{
    /**
     * The route to redirect if there is an error
     *
     * @var string
     */
    protected $onErrorRedirectTo = '/admin';

    /**
     * Initialize OAuth Controller
     *
     * @param \App\Innoclapps\OAuth\OAuthManager $manager
     */
    public function __construct(protected OAuthManager $manager)
    {
    }

    /**
     * Connect OAuth Account
     *
     * @param string $provider
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connect($provider)
    {
        $state = $this->manager->generateRandomState();

        OAuthState::put($state);

        return redirect($this->manager->createProvider($provider)
            ->getAuthorizationUrl(['state' => $state]));
    }

    /**
     * Callback for OAuth Account
     *
     * @param string $provider
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider, Request $request)
    {
        if ($request->error) {
            // Got an error, probably user denied access
            return redirect($this->onErrorRedirectTo)
                ->withErrors($request->error_description ?: $request->error);
        } elseif (! OAuthState::validate($request->state)) {
            return redirect($this->onErrorRedirectTo)
                ->withErrors(__('app.oauth.invalid_state'));
        }

        if ($request->has('code')) {
            try {
              $account=  $this->manager->forUser($request->user()->id)->connect($provider, $request->code);
            
            } catch (IdentityProviderException $e) {
                $message      = $e->getMessage();
                $responseBody = $e->getResponseBody();

                if ($responseBody instanceof Response) {
                    $responseBody = $responseBody->getReasonPhrase();
                }

                if ($responseBody != $message) {
                    $message .= ' [' . is_array($responseBody) ?
                        ($responseBody['error_description'] ?? $responseBody['error'] ?? json_encode($responseBody)) :
                        $responseBody . ']';
                }
                return redirect($this->onErrorRedirectTo)->withErrors($message);
            } catch (\Exception $e) {
                return redirect($this->onErrorRedirectTo)->withErrors($e->getMessage());
            }

            $returnUrl = OAuthState::getParameter('return_url', '/oauth/accounts');

            // Check if the account previously required authentication (for re-authenticate)
            if ((string) OAuthState::getParameter('re_auth') === '1') {
                Session::flash('success', __('app.oauth.re_authenticated'));
            }

            // Finally, forget the oauth state, the state is used in the listeners
            // to get parameters for the actual accounts data
            OAuthState::forget();

            return redirect($returnUrl);
        }
    }
}
