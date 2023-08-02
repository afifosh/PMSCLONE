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
use Modules\Core\Http\Controllers\Api\OAuthAccountController;
use Modules\Core\Http\Controllers\Api\PendingMediaController;
use Modules\Core\Http\Controllers\Api\Resource\MediaController;

// Route::middleware('auth:sanctum')->group(function () {
  Route::middleware('auth:admin')->group(function () {
    // OAuth accounts controller
    Route::apiResource('/oauth/accounts', OAuthAccountController::class, ['as' => 'oauth'])
        ->except(['store', 'update']);

    // Media routes
    Route::post('/media/pending/{draftId}', [PendingMediaController::class, 'store']);
    Route::delete('/media/pending/{pendingMediaId}', [PendingMediaController::class, 'destroy']);

    // Resource media routes
    // Route::post('{resource}/{resourceId}/media', [MediaController::class, 'store']);
    // Route::delete('{resource}/{resourceId}/media/{media}', [MediaController::class, 'destroy']);
});
