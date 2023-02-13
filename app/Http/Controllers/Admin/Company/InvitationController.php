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
      return $dataTable->render('admin.pages.company.invitations.index');
      return view('admin.pages.company.invitations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      // dd(date('Y-m-d h:i',  strtotime(now())));
      $contactPerson = new CompanyContactPerson();
      $roles = Role::where('guard_name', 'web')->pluck('name', 'id');
      $companies = Company::pluck('name', 'id')->prepend('Select Company', '');
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
    public function edit(CompanyInvitation $companyInvitation)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyInvitation  $companyInvitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyInvitation $companyInvitation)
    {
        //
    }
}
