<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\ContactsUpdateRequest;
use App\Models\CompanyContact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
  public function index()
  {
    if (request()->ajax()) {
      $data['contacts'] = auth()->user()->company->contacts;
      return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.index', $data)->render()]);
    }
  }

  public function create()
  {
    $data['contact'] = new CompanyContact();
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function store(ContactsUpdateRequest $request)
  {
    auth()->user()->company->contacts()->create($request->all());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }

  public function show(CompanyContact $companyContact)
  {
    //
  }

  public function edit($contact)
  {
    $data['contact'] = auth()->user()->company->contacts()->findOrFail($contact);
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function update(ContactsUpdateRequest $request, $contact)
  {
    auth()->user()->company->contacts()->findOrFail($contact)->update($request->all());
    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }

  public function destroy($contact)
  {
    auth()->user()->company->contacts()->findOrFail($contact)->delete();
    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }
}
