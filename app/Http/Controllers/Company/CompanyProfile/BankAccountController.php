<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\BankAccountUpdateRequest;
use App\Models\CompanyBankAccount;
use App\Models\Country;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
  public function index()
  {
    if (request()->ajax()) {
      $data['bankAccounts'] = auth()->user()->company->bankAccounts;
      return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.index', $data)->render()]);
    }
  }

  public function create()
  {
    $data['bank_account'] = new CompanyBankAccount();
    $data['countries'] = Country::pluck('name', 'id');
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function store(BankAccountUpdateRequest $request)
  {
    auth()->user()->company->bankAccounts()->create($request->all());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function show(CompanyBankAccount $companyBankAccount)
  {
    //
  }

  public function edit($bank_account)
  {
    $data['bank_account'] = auth()->user()->company->bankAccounts()->findOrFail($bank_account);
    $data['countries'] = Country::pluck('name', 'id');
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function update(Request $request, $bank_account)
  {
    auth()->user()->company->bankAccounts()->findOrFail($bank_account)->update($request->all());
    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function destroy($bank_account)
  {
    auth()->user()->company->bankAccounts()->findOrFail($bank_account)->delete();
    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }
}
