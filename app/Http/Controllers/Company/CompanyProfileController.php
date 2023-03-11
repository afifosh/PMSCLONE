<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\AddressUpdateRequest;
use App\Http\Requests\Company\CompanyProfile\BankAccountUpdateRequest;
use App\Http\Requests\Company\CompanyProfile\ContactsUpdateRequest;
use App\Http\Requests\Company\CompanyProfile\DetailsUpdateRequest;
use App\Models\CompanyAddress;
use App\Models\CompanyBankAccount;
use App\Models\CompanyContactPerson;
use App\Models\CompanyDetail;
use App\Models\Country;
use App\Repositories\FileUploadRepository;

class CompanyProfileController extends Controller
{
  public function editDetails()
  {
    $data['detail'] = auth()->user()->company->draftDetail ? auth()->user()->company->draftDetail->data :  auth()->user()->company->detail ?? new CompanyDetail;
    $data['contacts'] = auth()->user()->company->draftContacts ? auth()->user()->company->draftContacts->data :  (isset(auth()->user()->company->contacts[0]) ? auth()->user()->company->contacts : [new CompanyContactPerson]);
    $data['bankAccounts'] = auth()->user()->company->draftBankAccounts ? auth()->user()->company->draftBankAccounts->data :  (isset(auth()->user()->company->bankAccounts[0]) ? auth()->user()->company->bankAccounts : [new CompanyBankAccount]);
    $data['addresses'] = auth()->user()->company->draftAddresses ? auth()->user()->company->draftAddresses->data :  (isset(auth()->user()->company->addresses[0]) ? auth()->user()->company->addresses : [new CompanyAddress]);
    $data['form'] = 'company-details';
    $data['countries'] = Country::pluck('name', 'id');
    return view('pages.company-profile.edit', $data);
  }

  public function updateDetails(DetailsUpdateRequest $request, FileUploadRepository $fileRepo)
  {
    $att = $this->makeData($request, $fileRepo);
    if ($request->submit_type == 'submit') {
      auth()->user()->company->detail()->updateOrCreate(['company_id' => auth()->user()->company_id], $att);
      auth()->user()->company->draftDetail()->where('type', 'detail')->delete();
    } else {
      auth()->user()->company->draftDetail()->updateOrCreate(['type' => 'detail'], ['data' => $att]);
    }

    return $request->submit_type == 'submit' ? $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved As Draft', []);
  }

  protected function makeData($request, $fileRepo)
  {
    if ($request->hasFile('logo')) {
      $path = 'company/' . auth()->user()->company->id;
      $logo = $path . '/' . $fileRepo->addAttachment($request->file('logo'), $path);
    }
    $att = isset($logo) ? ['logo' => $logo] + $request->validated() : $request->validated();
    if (!isset($logo))
      unset($att['logo']);
    if (!$request->boolean('is_subsidory'))
      $att['parent_company'] = null;
    if (!$request->boolean('is_parent'))
      $att['subsidiaries'] = null;
    if (!$request->boolean('is_sa_available'))
      $att['sa_company_name'] = null;

    return $att;
  }

  public function updateContacts(ContactsUpdateRequest $request)
  {
    if ($request->submit_type == 'submit') {
      $available_contact_ids = [];
      foreach ($request->contacts as $contact){
        $available_contact_ids[] = auth()->user()->company->contacts()->updateOrCreate(['id' => $contact['id']], $contact)->id;
      }
      auth()->user()->company->draftContacts()->where('type', 'contacts')->delete();
      auth()->user()->company->contacts()->whereNotIn('id', $available_contact_ids)->delete();
    } else {
      auth()->user()->company->draftContacts()->updateOrCreate(['type' => 'contacts'], ['data' => $request->contacts]);
    }

    return $request->submit_type == 'submit' ? $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved As Draft', []);
  }

  public function updateBankAccounts(BankAccountUpdateRequest $request)
  {
    if ($request->submit_type == 'submit') {
      $available_bank_account_ids = [];
      foreach ($request->bank_accounts as $bank_account){
        $available_bank_account_ids[] = auth()->user()->company->bankAccounts()->updateOrCreate(['id' => $bank_account['id']], $bank_account)->id;
      }
      auth()->user()->company->draftBankAccounts()->where('type', 'bank_accounts')->delete();
      auth()->user()->company->bankAccounts()->whereNotIn('id', $available_bank_account_ids)->delete();
    } else {
      auth()->user()->company->draftBankAccounts()->updateOrCreate(['type' => 'bank_accounts'], ['data' => $request->bank_accounts]);
    }

    return $request->submit_type == 'submit' ? $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved As Draft', []);
  }

  public function updateAddresses(AddressUpdateRequest $request)
  {
    if ($request->submit_type == 'submit') {
      $available_address_ids = [];
      foreach ($request->addresses as $address){
        $available_address_ids[] = auth()->user()->company->addresses()->updateOrCreate(['id' => $address['id']], $address)->id;
      }
      auth()->user()->company->draftAddresses()->where('type', 'addresses')->delete();
      auth()->user()->company->addresses()->whereNotIn('id', $available_address_ids)->delete();
    } else {
      auth()->user()->company->draftAddresses()->updateOrCreate(['type' => 'addresses'], ['data' => $request->addresses]);
    }

    return $request->submit_type == 'submit' ? $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved As Draft', []);
  }
}
