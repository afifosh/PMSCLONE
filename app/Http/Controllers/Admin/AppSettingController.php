<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function storeGeneralSettings(Request $request) {
        $request->validate([
            'password_expire_days' => 'required|numeric|gt:1',
            'timeout_warning_seconds' => 'required|numeric|gte:3000',
            'timeout_after_seconds' => 'required|numeric|gt:timeout_warning_seconds',
        ]);

        AppSetting::updateOrCreate([
            'id' => 1,
        ], [
            'password_expire_days' => $request->password_expire_days,
            'timeout_warning_seconds' => $request->timeout_warning_seconds,
            'timeout_after_seconds' => $request->timeout_after_seconds,
        ]);

        return redirect()->back()->with(['status' => __('Settings updated successfully')]);
    }
}
