<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Jobs\Admin\InvitationMailJob;
use App\Mail\CompanyInvitationMail;
use App\Models\Company;
use App\Models\CompanyContactPerson;
use App\Models\Role;
use Illuminate\Http\Request;

class ContactPersonController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Company $company)
  {
    $contactPerson = new CompanyContactPerson();
    $roles = Role::where('guard_name', 'web')->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('admin.pages.company-contact.create', compact('company', 'contactPerson', 'roles'))->render()]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, Company $company)
  {
    $request->validate([
      'first_name' => 'required|max:255|string',
      'last_name' => 'required|max:255|string',
      'email' => 'required|unique:users,email|unique:company_contact_persons,email',
      'expiry_time' => 'required',
      'role' => 'required'
    ]);

    $contPerson = $company->contactPersons()->create($request->only('first_name', 'last_name', 'email'));
    $contPerson->invitations()->update(['status' => 'revoked']);
    $data = $contPerson->invitations()->create(['token' => bin2hex(random_bytes(16)), 'valid_till' => $request->expiry_time, 'role_id' => $request->role, 'status' => 'pending']);
    dispatch(new InvitationMailJob($data));

    return $this->sendRes('Added Successfully', ['close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\CompanyContactPerson  $companyContactPerson
   * @return \Illuminate\Http\Response
   */
  public function show(CompanyContactPerson $companyContactPerson)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\CompanyContactPerson  $companyContactPerson
   * @return \Illuminate\Http\Response
   */
  public function edit(CompanyContactPerson $companyContactPerson)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\CompanyContactPerson  $companyContactPerson
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, CompanyContactPerson $companyContactPerson)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\CompanyContactPerson  $companyContactPerson
   * @return \Illuminate\Http\Response
   */
  public function destroy(CompanyContactPerson $companyContactPerson)
  {
    //
  }
}
