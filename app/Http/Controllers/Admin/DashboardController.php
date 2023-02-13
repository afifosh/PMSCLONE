<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\AuthLogNotification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $current_guard = Auth::getDefaultDriver();
        if ($current_guard == 'admin') {
            $user = Auth::user();
            if ($user->checkIfLastLoginDetailsChanged()) {
                $user->notify(new AuthLogNotification($user->authentications, $user->lastLoginAgent()));
            }
        }
        return view('admin.dashboard');
    }
}
