<?php


use App\Http\Controllers\Core\{
  Setting\DeliverySettingController,
  Setting\SettingController
};
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/app')->name('admin.core.')->middleware('auth:admin', 'guest:web', 'adminVerified', 'mustBeActive', CheckForLockMode::class)->group(function () {

  Route::get('settings', [SettingController::class, 'index'])->name('settings.index');

  Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

  Route::get('settings/delivery-settings', [DeliverySettingController::class, 'index'])->name('settings.view-delivery');

  Route::post('settings/delivery-settings', [DeliverySettingController::class, 'update'])->name('settings.update-delivery');

  Route::get('settings/delivery-settings/show', [DeliverySettingController::class, 'show'])->name('settings.view_delivery');

  Route::any('settings/delivery-settings/send-test-email', [DeliverySettingController::class, 'sendTestEmail'])->name('settings.delivery.send-test-email');
});
