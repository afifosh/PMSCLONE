<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CompaniesDataTable;
use App\DataTables\Admin\Company\InvitationsDataTable;
use App\DataTables\Admin\Company\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CompanyController extends Controller
{

  function __construct()
  {
    $this->middleware('permission:read company|create company|update company|delete company', ['only' => ['index', 'show', 'showUsers', 'showInvitations']]);
    $this->middleware('permission:create company', ['only' => ['create', 'store']]);
    $this->middleware('permission:update company', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete company', ['only' => ['destroy']]);
  }

  public function index(CompaniesDataTable $datatable)
  {
    return $datatable->render('admin.pages.company.index');
    return view('admin.pages.company.index');
  }

  public function create()
  {
    $company = new Company();
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', compact('company'))->render()]);
  }

  public function store(Request $request)
  {
    // $type = $request->input('type');
    // dd(  $type);
    // dd($request->all());
    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
      // 'website' => ['required', 'string', 'max:255', 'unique:companies,website'],
      // 'email' => ['required', 'string', 'max:255', 'unique:companies,email'],
      // 'status' => 'required',
      'type' => 'required|in:Company,Person',
    ]);


    if (Company::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function show(Company $company, InvitationsDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);
      return $dataTable->render('admin.pages.company.show-profile', compact('company'));
  }

  public function showUsers(Company $company, UsersDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);
    $dataTable->company_id = $company->id;
    $data['statuses'] = User::distinct()->pluck('status', 'status');
    $data['roles'] = Role::where('guard_name', 'web')->distinct()->pluck('name');
    $data['company'] = $company;
    return $dataTable->render('admin.pages.company.users.index', $data);
    // return view('admin.pages.company.users.index');
  }

  public function showInvitations(Company $company, InvitationsDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);
    return $dataTable->render('admin.pages.company.show-invitations', compact('company'));
  }

  public function edit(Company $company)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', compact('company'))->render()]);
  }

  public function update(Request $request, Company $company)
  {
    // $att = $request->validate([
    //   'name' => ['required', 'string', 'max:255', Rule::unique('companies')->ignore($company->id),],
    //   'website' => ['required', 'string', 'max:255', Rule::unique('companies')->ignore($company->id),],
    //   'email' => ['required', 'string', 'max:255', Rule::unique('companies')->ignore($company->id),],
    //   'status' => 'required',
    // ]);

    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', Rule::unique('companies')->ignore($company->id),],
      // 'website' => ['required', 'string', 'max:255', 'unique:companies,website'],
      // 'email' => ['required', 'string', 'max:255', 'unique:companies,email'],
      // 'status' => 'required',
      'type' => 'required|in:Company,Person',
    ]);

    $company->update($att);
    // auth()->user()->approve($company->modifications()->first());
    // $company->approve('name');
    // if ($company->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID, 'close' => 'globalModal']);
    // }
  }

  public function destroy(Company $company)
  {
    try {
      if ($company->delete()) {
        return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID]);
      }
      return $this->sendError('Something Went Wrong');
    } catch (Throwable $e) {
      return $this->sendError('Server Error');
    }
  }
}
