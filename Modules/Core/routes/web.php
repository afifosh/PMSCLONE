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

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\ApplicationController;
use Modules\Core\Http\Controllers\MediaViewController;
use Modules\Core\Http\Controllers\MigrateController;
use Modules\Core\Http\Controllers\OAuthController;
use Modules\Core\Http\Middleware\PreventRequestsWhenMigrationNeeded;
use Modules\Core\Http\Middleware\PreventRequestsWhenUpdateNotFinished;

Route::withoutMiddleware([
    PreventRequestsWhenMigrationNeeded::class,
    PreventRequestsWhenUpdateNotFinished::class,
])->group(function () {
    Route::get('/errors/migration', [MigrateController::class, 'show']);
    Route::post('/errors/migration', [MigrateController::class, 'migrate']);

    Route::get('/media/{token}', [MediaViewController::class, 'show']);
    Route::get('/media/{token}/download', [MediaViewController::class, 'download']);
    Route::get('/media/{token}/preview', [MediaViewController::class, 'preview']);
});

Route::middleware('auth')->group(function () {
    Route::get('/{providerName}/connect', [OAuthController::class, 'connect'])->where('providerName', 'microsoft|google');
    Route::get('/{providerName}/callback', [OAuthController::class, 'callback'])->where('providerName', 'microsoft|google');
});


Route::middleware(['auth'])->group(function () {
    Route::fallback(ApplicationController::class);
});
