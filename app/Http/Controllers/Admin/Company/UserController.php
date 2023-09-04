<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\PartnerCompany;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Notifications\Admin\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
  public function index(Company $company, UsersDataTable $dataTable)
  {
    $company->load(['detail', 'addresses', 'bankAccounts', 'contacts', 'kycDocs']);
    $dataTable->company_id = $company->id;
    $data['statuses'] = User::distinct()->pluck('status', 'status');
    $data['roles'] = Role::where('guard_name', 'web')->distinct()->pluck('name');
    $data['company'] = $company;
    return $dataTable->render('admin.pages.company.users.index', $data);
    // return view('admin.pages.company.users.index');
  }

  public function create(Company $company)
  {
    $data['company'] = $company;
    $data['user'] = new User();
    $data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'id');
    $data['countries'] = ['' => 'Select Country'];
    $data['states'] = ['' => 'Select State'];
    $data['cities'] = ['' => 'Select City'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.users.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function store(Company $company, Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'nullable|phone',
      'phone_country' => 'required_with:phone',
      'email' => ['required', 'string', 'max:255', 'unique:users,email'],
      'pass-gen' => ['required', Rule::In(['auto', 'manually'])],
      'password' => ['nullable', 'string', 'max:255', 'min:6', Rule::requiredIf($request->input('pass-gen') == 'manually')],
      'status' => 'required',
      'roles' => 'required|exists:roles,id',
      'email_verified_at' => 'sometimes',
      'country_id' => 'nullable|exists:countries,id',
      'state_id' => 'nullable|exists:states,id',
      'city_id' => 'nullable|exists:cities,id',
      'job_title' => 'nullable|string|max:255',
      'can_login' => 'nullable',
      'gender' => ['nullable', Rule::In(['Male', 'Female', 'Other'])]
    ]);
    unset($att['roles']);
    $password = $request->input('pass-gen') == 'manually' ? $request->password : Str::random(15);
    $att['password'] = Hash::make($password);
    $att['email_verified_at'] = $request->boolean('email_verified_at') ? now() : null;
    $att['company_id'] = $company->id;
    $att['can_login'] = $request->boolean('can_login') ? 1 : 0;
    $att['is_primary'] = User::where('company_id', $company->id)->where('is_primary', 1)->exists() ? 0 : 1;
    $user = $company->users()->create($att);
    // $user->notify(new WelcomeNotification($password));
    $user->syncRoles([$request->roles]);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID, 'close' => 'globalModal']);
  }

  public function show(Company $company, User $user)
  {
    return view('admin.pages.roles.admins.show', compact('user'));
  }

  public function edit(Company $company, User $contact)
  {
    $data['company'] = $company;
    $data['user'] = $contact;
    $data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'id');
    $data['countries'] = $contact->country_id ? Country::where('id', $contact->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
    $data['states'] = $contact->state_id ? State::where('id', $contact->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
    $data['cities'] = $contact->city_id ? City::where('id', $contact->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.users.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  // public function editPassword(Admin $user)
  // {
  //   $data['user'] = $user;
  //   return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit-password', $data)->render()]);
  // }

  // public function updatePassword(Request $request, Admin $user)
  // {
  //   $request->validate([
  //     'password' => 'required|min:8|max:255|confirmed',
  //   ]);
  //   $user->update(['password' => Hash::make($request->password)]);
  //   return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID, 'close' => 'globalModal']);
  // }

  public function update(Company $company, User $contact, Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'nullable|phone',
      'phone_country' => 'required_with:phone',
      'email' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($contact->id),],
      'pass-gen' => ['required', Rule::In(['auto', 'manually'])],
      'password' => ['nullable', 'string', 'max:255', 'min:6', Rule::requiredIf($request->input('pass-gen') == 'manually')],
      'status' => 'required',
      'roles' => 'required|exists:roles,id',
      'email_verified_at' => 'sometimes',
      'country_id' => 'nullable|exists:countries,id',
      'state_id' => 'nullable|exists:states,id',
      'city_id' => 'nullable|exists:cities,id',
      'job_title' => 'nullable|string|max:255',
      'can_login' => 'nullable',
      'gender' => ['nullable', Rule::In(['Male', 'Female', 'Other'])]
    ]);
    unset($att['roles']);
    if($contact->email_verified_at && $request->boolean('email_verified_at')){
      unset($att['email_verified_at']);
    }else if(!$contact->email_verified_at && $request->boolean('email_verified_at')){
      $att['email_verified_at'] = now();
    }else if(!$request->boolean('email_verified_at')){
      $att['email_verified_at'] = null;
    }
    $att['password'] = $request->input('pass-gen') == 'manually' ? Hash::make($request->password) : '';
    if($request->input('pass-gen') != 'manually'){
      unset($att['password']);
    }
    $att['can_login'] = $request->boolean('can_login') ? 1 : 0;
    $contact->syncRoles([$request->roles]);
    if ($contact->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function destroy(Company $company, User $contact)
  {
    if ($contact->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID]);
    }
  }
}
