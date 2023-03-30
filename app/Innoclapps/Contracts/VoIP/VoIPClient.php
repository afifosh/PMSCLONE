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

namespace App\Innoclapps\Contracts\VoIP;

use Illuminate\Http\Request;
use App\Innoclapps\VoIP\Call;

interface VoIPClient
{
    /**
     * Validate the request for authenticity
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function validateRequest(Request $request);

    /**
     * Create new outgoing call from request
     *
     * @param string $phoneNumber
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function newOutgoingCall($phoneNumber, Request $request);

    /**
     * Create new incoming call from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function newIncomingCall(Request $request);

    /**
     * Get the Call class from the given webhook request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Innoclapps\VoIP\Call
     */
    public function getCall(Request $request) : Call;
}
