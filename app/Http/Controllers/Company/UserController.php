<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('company_id', auth()->user()->company_id)
              ->select('id', 'name', 'email', 'email_verified_at')
              ->with('roles');

            return Datatables::of($query)
              ->addIndexColumn()
              ->addColumn('action', function ($row) {
                  $btn = <<<'BTN'
                        <div class="d-inline-block text-nowrap">
                          <button class="btn btn-sm btn-icon edit-record" data-id="115" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-edit"></i></button>
                          <button class="btn btn-sm btn-icon delete-record" data-id="115"><i class="ti ti-trash"></i></button>
                          <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                          <div class="dropdown-menu dropdown-menu-end m-0">
                            <a href="#" class="dropdown-item">View</a>
                            <a href="javascript:;" class="dropdown-item">Suspend</a>
                          </div>
                        </div>
                        BTN;
                  return $btn;
              })
              ->editColumn('roles', function ($row) {
                  return $row->roles->pluck('name')->implode(', ');
              })
              ->addColumn('status', function ($row) {
                  return $row->email_verified_at
                    ? '<i class="ti fs-4 ti-shield-check text-success"></i>'
                    : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
              })
              ->rawColumns(['action', 'status'])
              ->make(true);
        }

        return view('pages.company-users');
    }
}
