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

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Modules\Activities\Http\Controllers\OAuthCalendarController;
use Modules\Activities\Http\Controllers\OutlookCalendarWebhookController;

Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::post('/webhook/outlook-calendar', [OutlookCalendarWebhookController::class, 'handle']);
});

Route::middleware('auth')->group(function () {
    Route::get('/calendar/sync/{provider}/connect', [OAuthCalendarController::class, 'connect']);
});
