<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\TaxesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
  public function index(TaxesDataTable $dataTable)
  {
    $data['title'] = $dataTable->type.'s';
    return $dataTable->render('admin.pages.finances.taxes.index', $data);
    // return view('admin.pages.finances.taxes.index');
  }

  public function retentions(TaxesDataTable $dataTable)
  {
    $dataTable->type = 'Retention';

    return $this->index($dataTable);
  }

  public function create()
  {
    $data['tax'] = new Tax();

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.taxes.create', $data)->render()]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:taxes,name',
      'type' => 'required|in:Fixed,Percent',
      'amount' => 'required|numeric|gt:0',
      'status' => 'required|in:Active,Inactive',
    ]);

    Tax::create($validated + ['is_retention' => $request->get('tax-type') == 'retention']);

    return $this->sendRes('Tax Added Successfully', ['event' => 'table_reload', 'table_id' => 'taxes-table', 'close' => 'globalModal']);
  }

  public function edit(Tax $tax)
  {
    $data['tax'] = $tax;

    return $this->sendRes('Tax Added Succefully', ['view_data' => view('admin.pages.finances.taxes.create', $data)->render()]);
  }

  public function update(Request $request, Tax $tax)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:taxes,name,' . $tax->id,
      'type' => 'required|in:Fixed,Percent',
      'amount' => 'required|numeric|gt:0',
      'status' => 'required|in:Active,Inactive',
    ]);

    $tax->update($request->all());

    return $this->sendRes('Tax updated successfuly', ['event' => 'table_reload', 'table_id' => 'taxes-table', 'close' => 'globalModal']);
  }

  public function destroy(Tax $tax)
  {
    $tax->delete();

    return $this->sendRes('Tax deleted successfully', ['event' => 'table_reload', 'table_id' => 'taxes-table']);
  }
}
