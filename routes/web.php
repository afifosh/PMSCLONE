<?php

use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\company\InvitationController;
use App\Http\Controllers\Company\UserAccountController;
use App\Http\Controllers\Company\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitations/{token}/confirm', [InvitationController::class, 'acceptConfirm'])->name('invitation.confirm');
Route::middleware('auth', 'verified', 'mustBeActive')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('pages-home');
    Route::resource('user-account', UserAccountController::class);

    Route::get('users/roles/{role}', [UserController::class, 'showRole']);
    Route::resource('users', UserController::class);

    Route::post('/keep-alive', fn() => response()->json(['status' => __('success')]));
});

require __DIR__.'/admin/admin.php';
