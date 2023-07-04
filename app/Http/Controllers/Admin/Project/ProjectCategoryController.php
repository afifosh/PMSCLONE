<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ProjectCategoriesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.projects.categories.index');
    view('admin.pages.projects.categories.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['category'] = new ProjectCategory();
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.categories.edit', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:project_categories,name',
    ]);

    ProjectCategory::create(['name' => $request->name]);

    return $this->sendRes('Category created successfully', ['event' => 'table_reload', 'table_id' => 'project-categories-datatable', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(ProjectCategory $projectCategory)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ProjectCategory $projectCategory)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.categories.edit', ['category' => $projectCategory])->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ProjectCategory $projectCategory)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:project_categories,name,' . $projectCategory->id . ',id',
    ]);

    $projectCategory->update(['name' => $request->name]);

    return $this->sendRes('Category updated successfully', ['event' => 'table_reload', 'table_id' => 'project-categories-datatable', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ProjectCategory $projectCategory)
  {
    if($projectCategory->projects()->count() > 0) {
      return $this->sendErr('Category is in use');
    }
    $projectCategory->delete();

    return $this->sendRes('Category deleted successfully', ['event' => 'table_reload', 'table_id' => 'project-categories-datatable']);
  }
}
