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
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Http\Resources\CountryResource;
use Modules\Core\Models\Country;

class CountryController extends ApiController
{
    /**
     * Get a list of all the application countries in storage.
     */
    public function handle(): JsonResponse
    {
        return $this->response(
            CountryResource::collection(Country::get())
        );
    }
}
