<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function storeGeneralSettings(Request $request) {
        $request->validate([
            'reset_password_days' => 'required',
        ]);

        AppSetting::updateOrCreate([
            'id' => 1,
        ], [
            'reset_password_days' => $request->reset_password_days
        ]);

        return redirect()->back()->with(['status' => __('Settings updated successfully')]);
    }
}
