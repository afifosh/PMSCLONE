<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Models\ApplicationCategory;
use Illuminate\Http\Request;

class ApplicationCategoryController extends Controller
{
  public function index(ApplicationCategoriesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.categories.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['category'] = new ApplicationCategory();
    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.categories.edit', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:application_categories,name',
    ]);

    ApplicationCategory::create(['name' => $request->name]);

    return $this->sendRes('Category created successfully', ['event' => 'table_reload', 'table_id' => 'application-categories-datatable', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(ApplicationCategory $applicationCategory)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ApplicationCategory $applicationCategory)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.categories.edit', ['category' => $applicationCategory])->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ApplicationCategory $applicationCategory)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:application_categories,name,' . $applicationCategory->id . ',id',
    ]);

    $applicationCategory->update(['name' => $request->name]);

    return $this->sendRes('Category updated successfully', ['event' => 'table_reload', 'table_id' => 'application-categories-datatable', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ApplicationCategory $applicationCategory)
  {
    if($applicationCategory->applications()->count() > 0) {
      return $this->sendErr('Category is in use');
    }

    try {
      $applicationCategory->delete();
    } catch (\Exception $e) {
      return $this->sendErr('Error deleting category: ' . $e->getMessage());
    }

    return $this->sendRes('Category  aaa deleted successfully', ['event' => 'table_reload', 'table_id' => 'application-categories-datatable']);
  }  
}
