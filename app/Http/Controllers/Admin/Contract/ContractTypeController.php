<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractTypesDataTable;
use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;

class ContractTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ContractTypesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.contract-types.index');
    // view('admin.pages.contract-types.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $contract_type = new ContractType();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contract-types.create', compact('contract_type'))->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:100'
    ]);

    ContractType::create($request->all());

    return $this->sendRes(__('Contract Type created successfully'), ['event' => 'table_reload', 'table_id' => 'contract-types-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(ContractType $contractType)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ContractType $contract_type)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contract-types.create', compact('contract_type'))->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ContractType $contract_type)
  {
    $request->validate([
      'name' => 'required|string|max:100'
    ]);

    $contract_type->update($request->all());

    return $this->sendRes(__('Contract Type updated successfully'), ['event' => 'table_reload', 'table_id' => 'contract-types-table', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ContractType $contract_type)
  {
    $contract_type->delete();

    return $this->sendRes(__('Contract Type deleted successfully'), ['event' => 'table_reload', 'table_id' => 'contract-types-table']);
  }
}
