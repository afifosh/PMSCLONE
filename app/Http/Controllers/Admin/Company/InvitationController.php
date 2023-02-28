<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\InvitationsDataTable;
use App\Http\Controllers\Controller;
use App\Jobs\Admin\InvitationMailJob;
use App\Models\Company;
use App\Models\CompanyContactPerson;
use App\Models\CompanyInvitation;
use App\Models\Role;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvitationsDataTable $dataTable)
    {
      $data['companies'] = Company::has('contactPersons.invitations')->pluck('name', 'id');
      $data['statuses'] = CompanyInvitation::distinct()->pluck('status');
      $roles = CompanyInvitation::distinct()->pluck('role_id');
      $data['roles'] = Role::whereIn('id', $roles)->pluck('name', 'id');
      // dd($data);
      return $dataTable->render('admin.pages.company.invitations.index', $data);
      return view('admin.pages.company.invitations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $contactPerson = new CompanyContactPerson();
      $roles = Role::where('guard_name', 'web')->pluck('name', 'id');
      $companies = $request->company ? Company::where('id', $request->company)->pluck('name', 'id') : Company::pluck('name', 'id')->prepend('Select Company', '');
      return $this->sendRes('success', ['view_data' => view('admin.pages.company.invitations.edit', compact('companies', 'contactPerson', 'roles'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // dd($request->all());
      $request->validate([
        'first_name' => 'required|max:255|string',
        'last_name' => 'required|max:255|string',
        'email' => 'required|email|unique:users,email|unique:company_contact_persons,email',
        'expiry_time' => 'required|date_format:Y-m-d h:i',
        'company_id' => 'required|exists:companies,id',
        'role' => 'required'
      ]);

      $contPerson = CompanyContactPerson::create($request->only('first_name', 'last_name', 'email', 'company_id'));
      $contPerson->invitations()->update(['status' => 'revoked']);
      $data = $contPerson->invitations()->create(['token' => bin2hex(random_bytes(16)), 'valid_till' => $request->expiry_time, 'role_id' => $request->role, 'status' => 'pending']);
      dispatch(new InvitationMailJob($data));
      $data->createLog('Invitation Created', $data->toArray());

      return $this->sendRes('Added Successfully', ['event' => 'table_reload', 'table_id' => CompanyInvitation::DT_ID, 'close' => 'globalModal']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyInvitation  $companyInvitation
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyInvitation $companyInvitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyInvitation  $companyInvitation
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, CompanyInvitation $companyInvitation)
    {
      // dd($companyInvitation->contactPerson);
      $contactPerson = $companyInvitation->contactPerson;
      $roles = Role::where('guard_name', 'web')->pluck('name', 'id');
      // $companies = $request->company ? Company::where('id', $request->company)->pluck('name', 'id') : Company::pluck('name', 'id')->prepend('Select Company', '');
      return $this->sendRes('success', ['view_data' => view('admin.pages.company.invitations.edit', compact('companyInvitation','contactPerson', 'roles'))->render()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyInvitation  $companyInvitation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyInvitation $companyInvitation)
    {
      $request->validate([
        'expiry_time' => 'required|date_format:Y-m-d h:i',
        'role' => 'required'
      ]);

      $contPerson = $companyInvitation->contactPerson;
      $contPerson->invitations()->update(['status' => 'revoked']);
      $data = $contPerson->invitations()->create(['token' => bin2hex(random_bytes(16)), 'valid_till' => $request->expiry_time, 'role_id' => $request->role, 'status' => 'pending']);
      dispatch(new InvitationMailJob($data));
      $data->createLog('Invitation Resent');

      return $this->sendRes('Added Successfully', ['event' => 'table_reload', 'table_id' => CompanyInvitation::DT_ID, 'close' => 'globalModal']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyInvitation  $companyInvitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyInvitation $companyInvitation)
    {
      if ($companyInvitation->delete()) {
        return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => CompanyInvitation::DT_ID]);
      }
    }

    public function revokeInvitation(Request $request, CompanyInvitation $companyInvitation)
    {
      if($request->method() == 'GET') {
        return $this->sendRes('success', ['view_data' => view('admin.pages.company.invitations.revoke', compact('companyInvitation'))->render()]);
      }
      $companyInvitation->status = 'revoked';
      $companyInvitation->createLog('Invitation revoked by '.auth()->user()->full_name);
      if ($companyInvitation->save()) {
        return $this->sendRes('Revoked Successfully', ['event' => 'table_reload', 'table_id' => CompanyInvitation::DT_ID, 'close' => 'modal']);
      }
    }
}
