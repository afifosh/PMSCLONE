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
      $data['countries'] = Country::pluck('name', 'id');
      $view_data = auth()->user()->company->isHavingPendingProfile() ? view('pages.company-profile.addresses.index', $data)->render()
        : view('pages.company-profile.new.detailed-content.addresses', $data)->render();

      return $this->sendRes('success', ['view_data' =>  $view_data]);
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
    if (request()->type == 'pending_creation') {
      $data['address'] = auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address);
    } else {
      $data['address'] = auth()->user()->company->addresses()->with('modifications.disapprovals')->findOrFail($address);
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['options'] = ['disabled' => 'disabled'];
    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function edit($address)
  {
    $data['address'] = request()->type == 'pending_creation' ? auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address)
      : auth()->user()->company->addresses()->with('modifications.disapprovals')->findOrFail($address);
    $data['countries'] = Country::pluck('name', 'id');

    return $this->sendRes('success', ['view_data' =>  view('pages.company-profile.addresses.create', $data)->render()]);
  }

  public function update(AddressUpdateRequest $request, $address)
  {
    if (request()->model_type == 'pending_creation') {
      $addr = auth()->user()->company->POCAddress()->where('is_update', false)->withCount('disapprovals')->findOrFail($address);
      $modifications = transformModifiedData($addr->modifications);
      unset($modifications['company_id']);
      if (empty(array_diff_assoc_recursive($modifications, $request->validated()))) {
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      }
      if ($addr->disapprovals_count > 0) {
        $addr->delete();
        auth()->user()->company->addresses()->create($request->validated());
      } else {
        $addr->updateModifications($request->validated());
      }
    } else {
      $addr = @auth()->user()->company->addresses()->with('modifications')->findOrFail($address);
      $mod = transformModifiedData(@$addr->modifications[0]->modifications ?? []) + $addr->toArray();
      unset($mod['company_id'], $mod['id'], $mod['created_at'], $mod['updated_at'], $mod['is_update'], $mod['modifications'], $mod['status']);
      if (empty(array_diff_assoc_recursive($mod, $request->validated()))) {
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
      }
      $addr->modifications()->delete();
      $addr->updateIfDirty($request->validated());
    }

    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }

  public function destroy($address)
  {
    if (request()->type == 'pending_creation') {
      auth()->user()->company->POCAddress()->where('is_update', false)->findOrFail($address)->delete();
    } else {
      auth()->user()->company->addresses()->findOrFail($address)->delete();
    }

    return $this->sendRes('Deleted Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 3]);
  }
}
