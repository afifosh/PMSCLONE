<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\ContactsUpdateRequest;
use App\Models\CompanyContact;
use App\Repositories\FileUploadRepository;
use App\Traits\ImageTrait;

class ContactController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable')->except(['index', 'show']);
  }

  public function index()
  {
    if (request()->ajax()) {
      $data['contacts'] = auth()->user()->company->contacts()->with('modifications', 'modifications.disapprovals')->get();
      $data['pending_creation_contacts'] = auth()->user()->company->POCContact()->where('is_update', false)->with('disapprovals')->get();
      $view_data = auth()->user()->company->isHavingPendingProfile() ? view('pages.company-profile.contacts.index', $data)->render()
        : view('pages.company-profile.new.detailed-content.contacts', $data)->render();

      return $this->sendRes('success', ['view_data' =>  $view_data]);
    }
  }

  public function create()
  {
    $data['contact'] = new CompanyContact();
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function store(ContactsUpdateRequest $request, FileUploadRepository $fileUploadRepository)
  {
    auth()->user()->company->contacts()->create($this->uploadPOA($request, $fileUploadRepository));
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 2]);
  }

  public function show($contact)
  {
    if (request()->type == 'pending_creation') {
      $data['contact'] = auth()->user()->company->POCContact()->where('is_update', false)->with('approvals', 'disapprovals')->findOrFail($contact);
    } else {
      $data['contact'] = auth()->user()->company->contacts()->with('modifications.disapprovals')->findOrFail($contact);
    }
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function edit($contact)
  {
    if (request()->type == 'pending_creation') {
      $data['contact'] = auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact);
    } else {
      $data['contact'] = auth()->user()->company->contacts()->with('modifications.disapprovals')->findOrFail($contact);
    }

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.contacts.create', $data)->render()]);
  }

  public function update(ContactsUpdateRequest $request, $contact, FileUploadRepository $fileRepository)
  {
    if (request()->model_type == 'pending_creation') {
      $cont = auth()->user()->company->POCContact()->where('is_update', false)->findOrFail($contact);
      if($request->hasFile('poa') || !$request->is_authorized)
        @$cont->modifications['poa']['modified'] ? ImageTrait::deleteImage($cont->modifications['poa']['modified'], 'public') : '';
      $att = $this->uploadPOA($request, $fileRepository) + ['poa' => @$cont->modifications['poa']['modified']];
      unset($att['is_authorized']);
      if($att['poa'] == null)
        unset($att['poa']);
      $cont_modifications = transformModifiedData($cont->modifications);
      if(empty(array_diff_assoc_recursive($att, $cont_modifications))){
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      }
      auth()->user()->company->contacts()->create($this->uploadPOA($request, $fileRepository) + ['poa' => @$cont->modifications['poa']['modified']]);
      $cont->delete();
    } else {
      $cont = auth()->user()->company->contacts()->findOrFail($contact)->modifications;
      if($request->hasFile('poa') || !$request->is_authorized)
      (!$cont->isEmpty() && @$cont[0]->modifications['poa']['modified']) ? ImageTrait::deleteImage($cont->modifications['poa']['modified'], 'public') : '';
      $t = (!$cont->isEmpty() && @$cont[0]->modifications['poa']['modified']) ? ['poa' => @$cont->modifications['poa']['modified']] : [];
      auth()->user()->company->contacts()->findOrFail($contact)->updateIfDirty($this->uploadPOA($request, $fileRepository) + $t);
      isset($cont[0]) ? $cont[0]->delete() : '';
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

  public function uploadPOA(ContactsUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $poa = [];
    $all = $request->validated();
    if ($request->hasFile('poa')) {
      $path = CompanyContact::POA_PATH . '/' . auth()->user()->company_id;
      $poa['poa'] = $path . '/' . $fileRepository->addAttachment($request->file('poa'), $path, 'public');
    }else{
      if(!$request->is_authorized){
        unset($all['poa']);
      }
    }
    return $poa  + $all;
  }
}
