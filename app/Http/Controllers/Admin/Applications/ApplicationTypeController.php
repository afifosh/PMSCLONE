<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationTypesDataTable;
use App\Http\Controllers\Controller;
use App\Models\ApplicationType;
use Illuminate\Http\Request;

class ApplicationTypeController extends Controller
{
  public function index(ApplicationTypesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.types.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['type'] = new ApplicationType();
    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.types.edit', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:application_types,name',
    ]);

    ApplicationType::create(['name' => $request->name]);

    return $this->sendRes('Type created successfully', ['event' => 'table_reload', 'table_id' => 'application-types-datatable', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(ApplicationType $type)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ApplicationType $type)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.types.edit', ['type' => $type])->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ApplicationType $type)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:application_types,name,' . $type->id . ',id',
    ]);

    $type->update(['name' => $request->name]);

    return $this->sendRes('Type updated successfully', ['event' => 'table_reload', 'table_id' => 'application-types-datatable', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ApplicationType $type)
  {
    if($type->applications()->count() > 0) {
      return $this->sendErr('Type is in use');
    }

    try {
      $type->delete();
    } catch (\Exception $e) {
      return $this->sendErr('Error deleting type: ' . $e->getMessage());
    }

    return $this->sendRes('Type deleted successfully', ['event' => 'table_reload', 'table_id' => 'application-types-datatable']);
  }    
}
