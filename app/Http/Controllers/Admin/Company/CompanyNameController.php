<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\HistoryNamesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ModelHistoryName;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyNameController extends Controller
{
  public function index(Company $company, HistoryNamesDataTable $dataTable)
  {
    $dataTable->company = $company;

    return $dataTable->render('admin.pages.company.history_names.index', compact('company'));
    // view('admin.pages.company.history_names.index', compact('company')
  }

  public function create(Company $company)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.history_names.create', compact('company'))->render()]);
  }

  public function store(Request $request, Company $company)
  {
    $att = $request->validate([
      'name' => ['nullable', 'required_if:name_ar,null', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
      'name_ar' => ['nullable', 'required_if:name,null', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
    ]);

    $company->update($att);

    return $this->sendRes('Name Updated Successfully', ['event' => 'table_reload', 'table_id' => 'historynames-table', 'close' => 'globalModal']);
  }

  public function edit(Company $company, ModelHistoryName $name)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.company.history_names.edit', compact('name'))->render()]);
  }

  public function update(Company $company, ModelHistoryName $name, Request $request)
  {
    $request->validate([
      'name' => 'required',
    ]);

    $name->update([
      'name' => $request->name,
    ]);

    return $this->sendRes('Name Updated Successfully', ['event' => 'table_reload', 'table_id' => 'historynames-table', 'close' => 'globalModal']);
  }
  public function destroy(Company $company, ModelHistoryName $name)
  {
    $name->delete();

    return $this->sendRes('Name Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'historynames-table']);
  }
}
