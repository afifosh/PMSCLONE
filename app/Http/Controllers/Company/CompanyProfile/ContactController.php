<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\ContactsUpdateRequest;
use App\Models\CompanyContact;

class ContactController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable')->except(['index', 'show']);
  }

  public function index()
  {
    if (request()->ajax()) {
      $data['contacts'] = auth()->user()->company->contacts;
      $data['pending_creation_contacts'] = auth()->user()->company->POCContact()->where('is_update', false)->get();
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
    auth()->user()->company->contacts()->create($request->validated());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }

  public function show($contact)
  {
    if(request()->type == 'pending_creation'){
      $data['contact'] = auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact);
    }else{
      $data['contact'] = auth()->user()->company->contacts()->findOrFail($contact);
    }
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function edit($contact)
  {
    if(request()->type == 'pending_creation'){
      $data['contact'] = auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact);
    }else{
      $data['contact'] = auth()->user()->company->contacts()->findOrFail($contact);
    }

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function update(ContactsUpdateRequest $request, $contact)
  {
    if (request()->model_type == 'pending_creation') {
      auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact)->delete();
      auth()->user()->company->contacts()->create($request->validated());
    }else{
      auth()->user()->company->contacts()->findOrFail($contact)->update($request->validated());
    }
    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }

  public function destroy($contact)
  {
    if (request()->type == 'pending_creation') {
      auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact)->delete();
    } else {
      auth()->user()->company->contacts()->findOrFail($contact)->delete();
    }
    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }
}
