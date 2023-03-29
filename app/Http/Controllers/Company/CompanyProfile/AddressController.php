<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\AddressUpdateRequest;
use App\Models\CompanyAddress;
use App\Models\Country;
use Illuminate\Http\Request;

class AddressController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable')->except(['index', 'show']);
  }

  public function index()
  {
    if (request()->ajax()) {
      $data['addresses'] = auth()->user()->company->addresses;
      $data['pending_addresses'] = auth()->user()->company->POCAddress()->where('is_update', false)->get();
      return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.index', $data)->render()]);
    }
  }

  public function create()
  {
    $data['address'] = new CompanyAddress();
    $data['countries'] = Country::pluck('name', 'id');
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function store(AddressUpdateRequest $request)
  {
    auth()->user()->company->addresses()->create($request->validated());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }

  public function show($address)
  {
    if(request()->type == 'pending_creation'){
      $data['address'] = auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address);
    }else{
      $data['address'] = auth()->user()->company->addresses()->findOrFail($address);
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function edit($address)
  {
    $data['address'] = request()->type == 'pending_creation' ? auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address)
      : auth()->user()->company->addresses()->findOrFail($address);
    $data['countries'] = Country::pluck('name', 'id');

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function update(AddressUpdateRequest $request, $address)
  {
    if(request()->model_type == 'pending_creation'){
      auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address)->delete();
      auth()->user()->company->addresses()->create($request->validated());
    }else{
      auth()->user()->company->addresses()->findOrFail($address)->modifications()->delete();
      auth()->user()->company->addresses()->findOrFail($address)->updateIfDirty($request->validated());
    }

    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }

  public function destroy($address)
  {
    if(request()->type == 'pending_creation'){
      auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address)->delete();
    }else{
      auth()->user()->company->addresses()->findOrFail($address)->delete();
    }

    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }
}
