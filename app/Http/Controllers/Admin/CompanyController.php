<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CompaniesDataTable;
use App\DataTables\Admin\Company\InvitationsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CompanyController extends Controller
{

  function __construct()
  {
    $this->middleware('permission:read company|create company|update company|delete company', ['only' => ['index', 'show']]);
    $this->middleware('permission:create company', ['only' => ['create', 'store']]);
    $this->middleware('permission:update company', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete company', ['only' => ['destroy']]);
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(CompaniesDataTable $datatable)
  {
    return $datatable->render('admin.pages.company.index');
    return view('admin.pages.company.index');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $company = new Company();
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', compact('company'))->render()]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', 'unique:companies,email'],
      'status' => 'required',
    ]);
    if(Company::create($att + ['added_by' => auth()->id()]))
    {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function show(Company $company, InvitationsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.company.view', compact('company'));
    return view('admin.pages.company.view', compact('company'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function edit(Company $company)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', compact('company'))->render()]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Company $company)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255',Rule::unique('admins')->ignore($company->id),],
      'status' => 'required',
    ]);
    if($company->update($att))
    {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
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
