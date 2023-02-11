<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordExpiredRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ExpiredPasswordController extends Controller
{
    /**
     * Reset password after password is expired
     * 
     * @param PasswordExpiredRequest $request
     * @return Redirect
     */
    public function resetPassword(PasswordExpiredRequest $request)
    {
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return redirect()->back()->withErrors(['current_password' => __('Current password is not correct')]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => Carbon::now()->toDateTimeString()
        ]);

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')->with(['status' => __('Password changed successfully')]);
        }

        return redirect()->route('pages-home')->with(['status' => __('Password changed successfully')]);
    }
}
