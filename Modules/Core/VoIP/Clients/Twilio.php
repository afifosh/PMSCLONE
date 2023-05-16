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

namespace Modules\Core\VoIP\Clients;

use Illuminate\Http\Request;
use Modules\Core\Contracts\VoIP\ReceivesEvents;
use Modules\Core\Contracts\VoIP\Tokenable;
use Modules\Core\Contracts\VoIP\VoIPClient;
use Modules\Core\VoIP\Call;
use Modules\Users\Models\User;
use Twilio\Jwt\ClientToken;
use Twilio\Security\RequestValidator;
use Twilio\TwiML\VoiceResponse;

class Twilio implements VoIPClient, ReceivesEvents, Tokenable
{
    /**
     * Holds the events URL
     *
     * @var string
     */
    protected $eventsUrl;

    /**
     * @var \Twilio\Jwt\ClientToken
     */
    protected $clientToken;

    /**
     * Initialize new Twilio instance.
     */
    public function __construct(protected array $config)
    {
        $this->clientToken = new ClientToken($config['accountSid'], $config['authToken']);
    }

    /**
     * Handle the VoIP service events request
     *
     *
     * @return \Twilio\TwiML\VoiceResponse
     */
    public function events(Request $request)
    {
        return $this->createResponse();
    }

    /**
     * Get the Call class from the given webhook request
     */
    public function getCall(Request $request): Call
    {
        return new Call(
            $this->number(),
            $request->input('From'),
            $request->input('To'),
            $request->input('DialCallStatus')
        );
    }

    /**
     * Create new outgoing call from request
     *
     * @param  string  $phoneNumber
     * @return \Twilio\TwiML\VoiceResponse
     */
    public function newOutgoingCall($phoneNumber, Request $request)
    {
        $response = $this->createResponse();

        $response->dial()
            ->setCallerId($this->number())
            ->setAction($this->eventsUrl)
            ->number($phoneNumber);

        return $response;
    }

    /**
     * Create new incoming call from request
     *
     *
     * @return \Twilio\TwiML\VoiceResponse
     */
    public function newIncomingCall(Request $request)
    {
        $response = $this->createResponse();
        $dial = $response->dial();

        $this->getLastActiveUsers()
            ->each(function ($user) use ($dial) {
                $dial->client($user->getKey());
            });

        $dial->setAction($this->eventsUrl);

        return $response;
    }

    /**
     * Set the events Url
     */
    public function setEventsUrl(string $url): static
    {
        $this->eventsUrl = $url;

        return $this;
    }

    /**
     * Create new client token for the logged in user
     *
     *
     * @return string
     */
    public function newToken(Request $request)
    {
        $this->clientToken->allowClientOutgoing($this->config['applicationSid']);

        // Set allowed incoming client, @see method newIncomingCall
        $this->clientToken->allowClientIncoming($request->user()->getKey());

        return $this->clientToken->generateToken($request->input('ttl', 3600));
    }

    /**
     * Validate the request for authenticity
     *
     *
     * @return void
     *
     * @see  https://www.twilio.com/docs/usage/security#http-authentication
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function validateRequest(Request $request)
    {
        if (! $signature = $request->server('HTTP_X_TWILIO_SIGNATURE')) {
            abort(403, 'The action you have requested is not allowed [Invalid signature].');
        }

        // Allow support for ngrok
        if ($ngrokHost = $this->viaNgrok($request)) {

            $url = $request->server('HTTP_X_FORWARDED_PROTO').'://'
                .$ngrokHost
                .$request->server('REQUEST_URI');

        } else {
            $url = $request->fullUrl();
        }

        $validator = new RequestValidator($this->config['authToken']);

        if (! $validator->validate($signature, $url, $_POST)) {
            abort(404);
        }
    }

    /**
     * Get the Twilio phone number
     *
     * @return string
     */
    public function number()
    {
        return $this->config['number'];
    }

    /**
     *  Get the users that were last active in the last 4 hours
     *  to be available as allowed users to receive calls for the client sdk
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getLastActiveUsers()
    {
        return User::where('last_active_at', '>=', now()->subHours(4))->get();
    }

    /**
     * Check if the given request is served via Ngrok
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function viaNgrok($request)
    {
        if ($request->server('HTTP_X_ORIGINAL_HOST')
            && str_contains($request->server('HTTP_X_ORIGINAL_HOST'), 'ngrok')) {
            return $request->server('HTTP_X_ORIGINAL_HOST');
        }

        // windows ngrok with the option --host-header
        if ($request->server('HTTP_X_FORWARDED_HOST')
            && str_contains($request->server('HTTP_X_FORWARDED_HOST'), 'ngrok')) {
            return $request->server('HTTP_X_FORWARDED_HOST');
        }

        return false;
    }

    /**
     * Create new voice response
     *
     * @return \Twilio\TwiML\VoiceResponse
     */
    protected function createResponse()
    {
        return new VoiceResponse;
    }
}
