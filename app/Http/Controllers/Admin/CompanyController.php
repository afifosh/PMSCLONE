<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CompaniesDataTable;
use App\DataTables\Admin\Company\InvitationsDataTable;
use App\DataTables\Admin\Company\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Role;
use App\Models\State;
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
    $data['company'] = new Company();
    $data['countries'] = ['' => 'Select Country'];
    $data['states'] = ['' => 'Select State'];
    $data['cities'] = ['' => 'Select City'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => ['nullable', 'required_if:name_ar,null', 'string', 'max:255', 'unique:companies,name'],
      'name_ar' => ['nullable', 'required_if:name,null', 'string', 'max:255', 'unique:companies,name_ar'],
      'website' => ['nullable', 'string', 'max:255', 'unique:companies,website'],
      'email' => ['nullable', 'string', 'max:255', 'unique:companies,email'],
      // 'status' => 'required',
      'type' => 'required|in:Company,Person',
      'phone' => 'nullable|phone',
      'phone_country' => 'required_with:phone',
      'address' => 'nullable|string|max:255',
      'city_id' => 'nullable|exists:cities,id',
      'state_id' => 'nullable|exists:states,id',
      'zip' => 'nullable|string|max:255',
      'country_id' => 'nullable|exists:countries,id',
      'vat_number' => 'nullable|string|max:255',
      'gst_number' => 'nullable|string|max:255',
    ]);


    if (Company::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Company::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function show(Company $company, InvitationsDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);

    return $dataTable->render('admin.pages.company.show-profile', compact('company'));
    // view('admin.pages.company.show-profile', compact('company'));
  }

  public function showInvitations(Company $company, InvitationsDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);

    return $dataTable->render('admin.pages.company.show-invitations', compact('company'));
  }

  public function edit(Company $company)
  {
    $data['company'] = $company;
    $data['countries'] = $company->country_id ? Country::where('id', $company->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
    $data['states'] = $company->state_id ? State::where('id', $company->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
    $data['cities'] = $company->city_id ? City::where('id', $company->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];

    return $this->sendRes('success', ['view_data' => view('admin.pages.company.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function update(Request $request, Company $company)
  {
    $att = $request->validate([
      'name' => ['nullable', 'required_if:name_ar,null', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
      'name_ar' => ['nullable', 'required_if:name,null', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
      'website' => ['nullable', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
      'email' => ['nullable', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
      // 'status' => 'required',
      'type' => 'required|in:Company,Person',
      'phone' => 'nullable|phone',
      'phone_country' => 'required_with:phone',
      'address' => 'nullable|string|max:255',
      'city_id' => 'nullable|exists:cities,id',
      'state_id' => 'nullable|exists:states,id',
      'zip' => 'nullable|string|max:255',
      'country_id' => 'nullable|exists:countries,id',
      'vat_number' => 'nullable|string|max:255',
      'gst_number' => 'nullable|string|max:255',
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
