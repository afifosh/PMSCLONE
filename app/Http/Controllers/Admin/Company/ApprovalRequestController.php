<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalRequestController extends Controller
{
    public function index()
    {
        return view('admin.company.approval-request.index');
    }

    public function getLevelReqeusts($level)
    {
      return view('admin.dashboard');
    }
}
