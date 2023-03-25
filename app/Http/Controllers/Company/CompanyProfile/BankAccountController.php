<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\BankAccountUpdateRequest;
use App\Models\CompanyBankAccount;
use App\Models\Country;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable')->except(['index', 'show']);
  }

  public function index()
  {
    if (request()->ajax()) {
      $data['bankAccounts'] = auth()->user()->company->bankAccounts;
      $data['pending_creation_accounts'] = auth()->user()->company->POCBankAccount()->where('is_update', false)->get();
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
    auth()->user()->company->bankAccounts()->create($request->validated());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function show($bank_account)
  {
    if (request()->type == 'pending_creation') {
      $data['bank_account'] = auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account);
    } else {
      $data['bank_account'] = auth()->user()->company->bankAccounts()->findOrFail($bank_account);
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function edit($bank_account)
  {
    $data['bank_account'] = request()->type == 'pending_creation' ? auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account)
      : auth()->user()->company->bankAccounts()->findOrFail($bank_account);
    $data['countries'] = Country::pluck('name', 'id');

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function update(BankAccountUpdateRequest $request, $bank_account)
  {
    if (request()->model_type == 'pending_creation') {
      auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account)->delete();
      auth()->user()->company->bankAccounts()->create($request->validated());
    } else {
      auth()->user()->company->bankAccounts()->findOrFail($bank_account)->update($request->validated());
    }
    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function destroy($bank_account)
  {
    if (request()->type == 'pending_creation') {
      auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account)->delete();
    } else {
      auth()->user()->company->bankAccounts()->findOrFail($bank_account)->delete();
    }
    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }
}
