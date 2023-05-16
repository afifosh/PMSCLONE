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
use Modules\Core\Highlights\Highlights;
use Modules\Core\Http\Controllers\ApiController;

class HighlightController extends ApiController
{
    /**
     * Get the application highlights.
     */
    public function __invoke(): JsonResponse
    {
        return $this->response(Highlights::get());
    }
}
