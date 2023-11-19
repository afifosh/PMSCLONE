<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\DataTables\Admin\WarehousesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warehouse\WarehouseStoreRequest;
use App\Models\Warehouse;
use App\Models\Location;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
  public function index(WarehousesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.warehouses.index');
  }

  public function create()
  {
    $data['warehouse'] = new Warehouse();
    $data['countries'] = ['' => 'Select Country'];
    $data['states'] = ['' => 'Select State'];
    $data['cities'] = ['' => 'Select City'];

    // Include location details
    $data['address'] = '';
    $data['latitude'] =  '';
    $data['longitude'] =  '';
    $data['zoomLevel'] =  '';

    return $this->sendRes('success', ['view_data' => view('admin.pages.warehouses.create', $data)->render()]);
  }

//   public function store(WarehouseStoreRequest $request)
//   {
//     // Assuming $request has location data, create or find the Location
//     $location = Location::create([
//       // ... fields from $request for the location ...
//   ]);

//   // Now create the Warehouse with the location_id
//   Warehouse::create(array_merge($request->validated(), ['location_id' => $location->id]));

//   return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'warehouses-table', 'close' => 'globalModal']);
// }
  public function store(WarehouseStoreRequest $request)
  {
      // Start the database transaction
      DB::beginTransaction();

      try {
          // Extract location data from the request
          $locationData = [
              'country_id' => $request->input('country_id'),
              'state_id' => $request->input('state_id'),
              'city_id' => $request->input('city_id'),
              'address' => $request->input('address'),
              'latitude' => $request->input('latitude'),
              'longitude' => $request->input('longitude'),
              'zoomLevel' => $request->input('zoomLevel'),
              'owner_type' => $request->input('owner_type'),
              'owner_id' => $request->input('owner_id'),
              'is_public' => false, // Set is_public to false
              'is_warehouse' => true, // Set is_warehouse to true
              'added_by' => $request->input('added_by'),
              'status' => $request->input('status'),
          ];

          // Create the Location
          $location = Location::create($locationData);

          // Merge additional data with the validated request data
          $warehouseData = array_merge($request->validated(), [
            'location_id' => $location->id,
            'owner_type' => $request->input('owner_type'),
            'owner_id' => $request->input('owner_id'),
            'added_by' => $request->input('added_by'),
            'status' => $request->input('status'),
          ]);

          // Create the Warehouse
          Warehouse::create($warehouseData);


          // Commit the transaction
          DB::commit();

          // Return success response
          return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'warehouses-table', 'close' => 'globalModal']);
      } catch (Exception $e) {
          // Rollback the transaction in case of an error
          DB::rollback();

          // Return error response
          return $this->sendError('Something Went Wrong' . $e->getMessage());
          // return $this->sendRes('Error: ' . $e->getMessage(), ['event' => 'error'], 500);
      }
  }

  public function show(Warehouse $warehouse)
  {
    $data['warehouse'] = $warehouse;

    return view('admin.pages.warehouses.show', $data);
  }


  public function edit(Warehouse $warehouse)
  {
      $data['warehouse'] = $warehouse;
      $data['countries'] = [$warehouse->location->country_id => $warehouse->location->country->name ?? 'Select Country'];
      $data['states'] = [$warehouse->location->state_id => $warehouse->location->state->name ?? 'Select State'];
      $data['cities'] = [$warehouse->location->city_id => $warehouse->location->city->name ?? 'Select City'];
      $data['owners'] = [$warehouse->owner_id => $warehouse->owner->name ?? 'Select Owner'];
      $data['ownerType'] = $warehouse->owner_type == 'App\Models\PartnerCompany' ? 'PartnerCompany' : ($warehouse->owner ?  ($warehouse->owner->type == 'Company' ? 'Company' : 'Client') : 'Select Owner');
  
      // Include location details
      $data['address'] = $warehouse->location->address ?? '';
      $data['latitude'] = $warehouse->location->latitude ?? '';
      $data['longitude'] = $warehouse->location->longitude ?? '';
      $data['zoomLevel'] = $warehouse->location->zoomLevel ?? '';
  
      return $this->sendRes('success', ['view_data' => view('admin.pages.warehouses.create', $data)->render()]);
  }

  public function update(Warehouse $warehouse, WarehouseStoreRequest $request)
  {
      // Start a database transaction
      DB::beginTransaction();
  
      try {

          $warehouseData = $request->validated(); // Adjust as necessary
          // Retrieve the associated Location and update it
          $locationData = [
            'country_id' => $request->input('country_id'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'zoomLevel' => $request->input('zoomLevel'),
            'owner_type' => $request->input('owner_type'),
            'owner_id' => $request->input('owner_id'),
            'added_by' => $request->input('added_by'),
            'status' => $request->input('status'),
          ];
  
          $warehouse->location()->update($locationData);
  
          // Update the Warehouse with any other relevant data
          
          $warehouse->update($warehouseData);
  
          // Commit the transaction
          DB::commit();
  
          // Return success response
          return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'warehouses-table', 'close' => 'globalModal']);
      } catch (Exception $e) {
          // Rollback the transaction in case of an error
          DB::rollback();
  
          // Handle the error appropriately
          return $this->sendError('Something Went Wrong' . $e->getMessage());
      }
  }
  

  public function destroy(Warehouse $warehouse)
  {
    $warehouse->delete();

    return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'warehouses-table']);
  }
}
