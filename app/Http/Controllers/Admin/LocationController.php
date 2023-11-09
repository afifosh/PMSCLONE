<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\LocationsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Location\LocationStoreRequest;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
  public function index(LocationsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.locations.index');
    // view('admin.pages.locations.index');
  }

  public function create()
  {
    $data['location'] = new Location();
    $data['countries'] = ['' => 'Select Country'];
    $data['states'] = ['' => 'Select State'];
    $data['cities'] = ['' => 'Select City'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.locations.create', $data)->render()]);
  }

  public function store(LocationStoreRequest $request)
  {
    Location::create($request->validated());

    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'locations-table', 'close' => 'globalModal']);
  }

  public function show(Location $location)
  {
    $data['location'] = $location;

    return view('admin.pages.locations.show', $data);
  }


  public function edit(Location $location)
  {
    $data['location'] = $location;
    $data['countries'] = [$location->country_id => $location->country->name ?? 'Select Country'];
    $data['states'] = [$location->state_id => $location->state->name ?? 'Select State'];
    $data['cities'] = [$location->city_id => $location->city->name ?? 'Select City'];
    $data['owners'] = [$location->owner_id => $location->owner->name ?? 'Select Owner'];
    $data['ownerType'] = $location->owner_type == 'App\Models\PartnerCompany' ? 'PartnerCompany' : ($location->owner ?  ($location->owner->type == 'Company' ? 'Company' : 'Client') : 'Select Owner');

    return $this->sendRes('success', ['view_data' => view('admin.pages.locations.create', $data)->render()]);
  }

  public function update(Location $location, LocationStoreRequest $request)
  {
    $location->update($request->validated());

    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'locations-table', 'close' => 'globalModal']);
  }


  public function destroy(Location $location)
  {
    $location->delete();

    return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'locations-table']);
  }
}
