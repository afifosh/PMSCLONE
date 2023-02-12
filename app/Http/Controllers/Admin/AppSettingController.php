<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function storeGeneralSettings(Request $request) {
        $request->validate([
            'password_expire_days' => 'required|number|gt:1',
        ]);

        AppSetting::updateOrCreate([
            'id' => 1,
        ], [
            'password_expire_days' => $request->password_expire_days
        ]);

        return redirect()->back()->with(['status' => __('Settings updated successfully')]);
    }
}
