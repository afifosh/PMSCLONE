<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppSetting\GeneralSettingRequest;
use App\Models\AppSetting;
use App\Models\EmailService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AppSettingController extends Controller
{
    /**
     * Returns app setting page
     * 
     * @return View
     */
    public function index()
    {
        $generalSettings = AppSetting::first();
        $emailServices = EmailService::with('emailServiceFields')->get();


        return view(
            'admin.app-setting',
            compact('emailServices', 'generalSettings')
        );
    }

    /**
     * Stores / Updates general settings tab
     * 
     * @param GeneralSettingRequest $request
     * @return Redirect
     */
    public function storeGeneralSettings(GeneralSettingRequest $request)
    {
        AppSetting::updateOrCreate(['id' => 1], [
            'password_expire_days' => $request->password_expire_days,
            'timeout_warning_seconds' => $request->timeout_warning_seconds,
            'timeout_after_seconds' => $request->timeout_after_seconds,
        ]);

        cache()->store(config('cache.default'))->put(
            'timeout_warning_seconds',
            $request->timeout_warning_seconds
        );

        cache()->store(config('cache.default'))->put(
            'timeout_after_seconds',
            $request->timeout_after_seconds
        );

        return redirect()->back()->with(['status' => __('Settings updated successfully')]);
    }
}
