<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\BankAccountUpdateRequest;
use App\Models\CompanyBankAccount;
use App\Models\Country;
use App\Repositories\FileUploadRepository;
use App\Traits\ImageTrait;

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
      $data['countries'] = Country::pluck('name', 'id');
      $view_data = auth()->user()->company->isHavingPendingProfile() ? view('pages.company-profile.bank-accounts.index', $data)->render()
        : view('pages.company-profile.new.detailed-content.accounts', $data)->render();

      return $this->sendRes('success', ['view_data' =>  $view_data]);
    }
  }

  public function create()
  {
    $data['bank_account'] = new CompanyBankAccount();
    $data['countries'] = Country::pluck('name', 'id');
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function store(BankAccountUpdateRequest $request, FileUploadRepository $fileUploadRepository)
  {
    auth()->user()->company->bankAccounts()->create($this->uploadBankLetter($request, $fileUploadRepository));
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function show($bank_account)
  {
    if (request()->type == 'pending_creation') {
      $data['bank_account'] = auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account);
    } else {
      $data['bank_account'] = auth()->user()->company->bankAccounts()->with('modifications.disapprovals')->findOrFail($bank_account);
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function edit($bank_account)
  {
    $data['bank_account'] = request()->type == 'pending_creation' ? auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account)
      : auth()->user()->company->bankAccounts()->with('modifications.disapprovals')->findOrFail($bank_account);
    $data['countries'] = Country::pluck('name', 'id');

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.bank-accounts.create', $data)->render()]);
  }

  public function update(BankAccountUpdateRequest $request, $bank_account, FileUploadRepository $fileRepository)
  {
    if (request()->model_type == 'pending_creation') {
      $account = auth()->user()->company->POCBankAccount()->where('is_update', false)->withCount('disapprovals')->findOrFail($bank_account);
      $PModifications = transformModifiedData($account->modifications);
      unset($PModifications['company_id']);
      // if(empty(array_diff_assoc($PModifications, $request->validated()))){
      //   return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      // }
      // auth()->user()->company->POCBankAccount()->where('is_update', false)->findOrFail($bank_account)->delete();
      // auth()->user()->company->bankAccounts()->create($request->validated());

      // $cont = auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact);
      if ($request->hasFile('bank_letter') || !$request->is_authorized)
        @$PModifications['bank_letter'] ? ImageTrait::deleteImage($PModifications['bank_letter'], 'public') : '';
      $att = $this->uploadBankLetter($request, $fileRepository) + ['bank_letter' => @$PModifications['bank_letter']];
      unset($att['is_authorized']);
      if ($att['bank_letter'] == null)
        unset($att['bank_letter']);
      // $cont_modifications = transformModifiedData($cont->modifications);
      if (empty(array_diff_assoc_recursive($att, $PModifications))) {
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      }
      if ($account->disapprovals_count > 0) {
        $account->delete();
        auth()->user()->company->bankAccounts()->create($this->uploadBankLetter($request, $fileRepository) + ['bank_letter' => @$account['bank_letter']]);
      } else {
        $account->updateModifications($this->uploadBankLetter($request, $fileRepository) + ['bank_letter' => @$account['bank_letter']]);
      }
      // $cont->delete();
    } else {
      // auth()->user()->company->bankAccounts()->findOrFail($bank_account)->modifications()->delete();
      // auth()->user()->company->bankAccounts()->findOrFail($bank_account)->updateIfDirty($request->validated());
      $ba = @auth()->user()->company->contacts()->with('modifications')->findOrFail($bank_account);
      $mod = transformModifiedData(@$ba->modifications[0]->modifications ?? []) + $ba->toArray();
      if ($mod['bank_letter'] == null)
        unset($mod['bank_letter']);
      unset($mod['company_id'], $mod['id'], $mod['created_at'], $mod['updated_at'], $mod['is_update'], $mod['modifications'], $mod['status']);

      $modifications = $ba->modifications;

      if ($request->hasFile('bank_letter') || !$request->is_authorized) (!$modifications->isEmpty() && @$modifications[0]->modifications['bank_letter']['modified']) ? ImageTrait::deleteImage($modifications[0]->modifications['bank_letter']['modified'], 'public') : '';
      $t = (!$modifications->isEmpty() && @$modifications[0]->modifications['bank_letter']['modified']) ? ['bank_letter' => @$modifications[0]->modifications['bank_letter']['modified']] : [];

      $new_att = $this->uploadBankLetter($request, $fileRepository) + $t;
      if (empty(array_diff_assoc_recursive($mod, $new_att))) {
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      }

      $ba->updateIfDirty($new_att);
      isset($modifications[0]) ? $modifications[0]->delete() : '';
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

  public function uploadBankLetter(BankAccountUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $bank_letter = [];
    $all = $request->validated();
    if ($request->hasFile('bank_letter')) {
      $path = CompanyBankAccount::BANK_LETTER_PATH . '/' . auth()->user()->company_id;
      $bank_letter['bank_letter'] = $path . '/' . $fileRepository->addAttachment($request->file('bank_letter'), $path, 'public');
    } else {
      if (!$request->is_authorized) {
        unset($all['bank_letter']);
      }
    }
    return $bank_letter  + $all;
  }
}
