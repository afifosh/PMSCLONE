<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\BankAccountUpdateRequest;
use App\Models\CompanyBankAccount;
use App\Models\Country;
use Illuminate\Http\Request;

class BankAccountController extends Controller
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
      $data['bank_account'] = new CompanyBankAccount();
      $data['countries'] = Country::pluck('name', 'id');
      return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankAccountUpdateRequest $request)
    {
      auth()->user()->company->bankAccounts()->create($request->all());
      return $this->sendRes('Added Successfully', ['event' => 'page_reload']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyBankAccount  $companyBankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyBankAccount $companyBankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyBankAccount  $companyBankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyBankAccount $companyBankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyBankAccount  $companyBankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyBankAccount $companyBankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyBankAccount  $companyBankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyBankAccount $companyBankAccount)
    {
        //
    }
}
