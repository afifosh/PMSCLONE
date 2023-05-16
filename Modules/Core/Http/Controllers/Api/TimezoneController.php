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

namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Facades\Timezone;
use Modules\Core\Http\Controllers\ApiController;

class TimezoneController extends ApiController
{
    /**
     * Get a list of all timezones.
     */
    public function handle(): JsonResponse
    {
        return $this->response(Timezone::toArray());
    }
}
