<?php

use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\API\ChatAPIController;
use Modules\Chat\Http\Controllers\API\GroupAPIController;
use Modules\Chat\Http\Controllers\API\UserAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/chat', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:api'])->group(function () {
  Route::post('broadcasting/auth', [BroadcastController::class, 'authenticate']);
//   // Route::get('logout', [AuthAPIController::class, 'logout']);

//   //get all user list for chat
//   Route::get('users-list', [UserAPIController::class, 'getUsersList']);
//   Route::post('change-password', [UserAPIController::class, 'changePassword']);

//   Route::get('profile', [UserAPIController::class, 'getProfile'])->name('my-profile');
//   Route::post('profile', [UserAPIController::class, 'updateProfile']);
//   Route::post('update-last-seen', [UserAPIController::class, 'updateLastSeen']);

//   // Route::post('send-message', [ChatAPIController::class, 'sendMessage'])->name('conversations.store');
//   Route::get('users/{id}/conversation', [UserAPIController::class, 'getConversation']);
//   Route::get('conversations', [ChatAPIController::class, 'getLatestConversations']);
//   Route::post('read-message', [ChatAPIController::class, 'updateConversationStatus']);
//   Route::post('file-upload', [ChatAPIController::class, 'addAttachment'])->name('file-upload');
//   Route::get('conversations/{userId}/delete', [ChatAPIController::class, 'deleteConversation']);

//   /** Update Web-push */
//   Route::put('update-web-notifications', [UserAPIController::class, 'updateNotification']);

//   /** create group **/
//   Route::post('groups', [GroupAPIController::class, 'create'])->name('create-group');

// });
