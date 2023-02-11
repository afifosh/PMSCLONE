<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\PasswordExpiredRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ExpiredPasswordController extends Controller
{
    /**
     * Returns password expired view
     * 
     * @return View
     */
    public function index()
    {
        return view('auth.expired-password');
    }

    /**
     * Reset password after password is expired
     * 
     * @param PasswordExpiredRequest $request
     * @return Redirect
     */
    public function resetPassword(PasswordExpiredRequest $request)
    {
        if (! Hash::check($request->current_password, $request->user()->password)) {
            return redirect()->back()->withErrors(['current_password' => __('Current password is not correct')]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => Carbon::now()->toDateTimeString()
        ]);

        return redirect()->intended()->with(['status' => __('Password changed successfully')]);
    }
}
