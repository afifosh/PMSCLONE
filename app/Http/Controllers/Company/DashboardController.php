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
    return view('pages.dashboard');
  }
}
