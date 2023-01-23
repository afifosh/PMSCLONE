<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    public function index()
    {
      $data['roles'] = Role::where('guard_name', 'admin')->withCount('users')->get();
      return view('admin.roles', $data);
    }
}
