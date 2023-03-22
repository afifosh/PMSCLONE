<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\AddressUpdateRequest;
use App\Models\CompanyAddress;
use App\Models\Country;
use Illuminate\Http\Request;

class AddressController extends Controller
{
  public function index()
  {
    if (request()->ajax()) {
      $data['addresses'] = auth()->user()->company->addresses;
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
    auth()->user()->company->addresses()->create($request->all());
    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }

  public function show(CompanyAddress $companyAddress)
  {
    //
  }

  public function edit($address)
  {
    $data['address'] = auth()->user()->company->addresses()->findOrFail($address);
    $data['countries'] = Country::pluck('name', 'id');
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function update(AddressUpdateRequest $request, $address)
  {
    auth()->user()->company->addresses()->findOrFail($address)->update($request->all());
    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }

  public function destroy($address)
  {
    auth()->user()->company->addresses()->findOrFail($address)->delete();
    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }
}
