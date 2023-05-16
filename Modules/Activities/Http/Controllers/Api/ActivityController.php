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

namespace Modules\Activities\Http\Controllers\Api;

use Illuminate\Http\Response;
use Modules\Activities\Models\Activity;
use Modules\Core\Http\Controllers\ApiController;

class ActivityController extends ApiController
{
    /**
     * Download ICS of the given activity.
     */
    public function downloadICS(Activity $activity): Response
    {
        $this->authorize('view', $activity);

        return response($activity->generateICSInstance()->get(), 200, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'attachment; filename='.$activity->icsFilename().'.ics',
            'charset' => 'utf-8',
        ]);
    }
}
