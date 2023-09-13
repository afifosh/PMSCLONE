<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Models\ContractCategory;
use Illuminate\Http\Request;

class ContractCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ContractCategoriesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.contract-categories.index');
    // view('admin.pages.contract-categories.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $contract_category = new ContractCategory();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contract-categories.create', compact('contract_category'))->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:100'
    ]);

    ContractCategory::create($request->all());

    return $this->sendRes(__('Contract Category created successfully'), ['event' => 'table_reload', 'table_id' => 'contract-categories-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(ContractCategory $ContractCategory)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ContractCategory $contract_category)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contract-categories.create', compact('contract_category'))->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ContractCategory $contract_category)
  {
    $request->validate([
      'name' => 'required|string|max:100'
    ]);

    $contract_category->update($request->all());

    return $this->sendRes(__('Contract Category updated successfully'), ['event' => 'table_reload', 'table_id' => 'contract-categories-table', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ContractCategory $contract_category)
  {
    $contract_category->delete();

    return $this->sendRes(__('Contract Category deleted successfully'), ['event' => 'table_reload', 'table_id' => 'contract-categories-table']);
  }
}
