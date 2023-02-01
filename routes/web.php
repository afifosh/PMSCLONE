<?php

use App\Http\Controllers\Company\DashboardController;
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

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('pages-home');
    Route::resource('user-account', UserAccountController::class);
    Route::resource('company-users', UserController::class);
});

require __DIR__.'/admin/admin.php';
