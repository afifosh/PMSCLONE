<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\TaxesDataTable;
use App\Http\Controllers\Controller;
use App\Models\InvoiceConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

  public function downpayments(TaxesDataTable $dataTable)
  {
    $dataTable->type = 'Down Payment';

    return $this->index($dataTable);
  }

  public function create()
  {
    $data['tax'] = new InvoiceConfig();

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.taxes.create', $data)->render()]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:invoice_configs,name',
      'type' => 'required|in:Fixed,Percent',
      'amount' => 'required|numeric|gt:0',
      'status' => 'required|in:Active,Inactive',
      'tax-type' => 'required|in:Tax,Retention,Down Payment',
      'category' => 'nullable|required_if:tax-type,Tax|in:1,2,3'
    ]);

    InvoiceConfig::create($validated + ['config_type' => $request->get('tax-type')]);

    return $this->sendRes('Tax Added Successfully', ['event' => 'table_reload', 'table_id' => 'taxes-table', 'close' => 'globalModal']);
  }

  public function edit(InvoiceConfig $tax)
  {
    $data['tax'] = $tax;

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.taxes.create', $data)->render()]);
  }

  public function update(Request $request, InvoiceConfig $tax)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:invoice_configs,name,' . $tax->id,
      'type' => 'required|in:Fixed,Percent',
      'amount' => 'required|numeric|gt:0',
      'status' => 'required|in:Active,Inactive',
      'category' => ['nullable', Rule::requiredIf($tax->config_type == 'Tax'), Rule::in([1, 2, 3])]
    ]);

    $tax->update($request->all());

    return $this->sendRes('Tax updated successfuly', ['event' => 'table_reload', 'table_id' => 'taxes-table', 'close' => 'globalModal']);
  }

  public function destroy(InvoiceConfig $tax)
  {
    $tax->delete();

    return $this->sendRes('Tax deleted successfully', ['event' => 'table_reload', 'table_id' => 'taxes-table']);
  }
}
