<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AuthLogNotification;

class DashboardController extends Controller
{
  public function index()
  {
    $current_guard = Auth::getDefaultDriver();
    if ($current_guard == 'web') {
      $user = Auth::user();
      if ($user->checkIfLastLoginDetailsChanged()) {
        $user->notify(new AuthLogNotification($user->authentications, $user->lastLoginAgent()));
      }
    }
    return view('pages.dashboard');
  }
}
