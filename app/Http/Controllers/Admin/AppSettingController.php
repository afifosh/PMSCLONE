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
    public function security(): View
    {
        $generalSettings = AppSetting::first();

        return view('admin.pages.settings.security.index', compact('generalSettings'));
    }

    public function general(): View
    {
        return view('admin.pages.settings.general.index');
    }

    /**
     * Email settings
     * @return View
     */
    public function email(): View
    {
        $emailServices = EmailService::query()->get();

        return view('admin.pages.settings.emails.index', compact('emailServices'));
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
            'password_history_count' => $request->password_history_count,
            'password_expire_days' => $request->password_expire_days,
            'timeout_warning_seconds' => $request->timeout_warning_seconds,
            'timeout_after_seconds' => $request->timeout_after_seconds,
        ]);

        cache()->store(config('cache.default'))->put(
            'idle_timeout_settings',
            json_encode(
                $request->only(['timeout_warning_seconds', 'timeout_after_seconds'])
            ),
        );

        return redirect()->back()->with(['status' => __('Settings updated successfully')]);
    }
}
