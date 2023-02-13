<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\EmailService;
use Illuminate\Http\Request;
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
        $email_services = EmailService::with('emailServiceFields')->get();

        return view(
            'admin.app-setting',
            compact('email_services')
        );
    }

    /**
     * Stores / Updates general settings tab
     * 
     * @param Request $request
     * @return Redirect
     */
    public function storeGeneralSettings(Request $request)
    {
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
