<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\ContactsUpdateRequest;
use App\Models\CompanyContact;
use Illuminate\Http\Request;

class ContactController extends Controller
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
    public function create()
    {
      $data['contact'] = new CompanyContact();
      return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactsUpdateRequest $request)
    {
      auth()->user()->company->addresses()->create($request->all());
      return $this->sendRes('Added Successfully', ['event' => 'page_reload']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyContact  $companyContact
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyContact $companyContact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyContact  $companyContact
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyContact $companyContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyContact  $companyContact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyContact $companyContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyContact  $companyContact
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyContact $companyContact)
    {
        //
    }
}
