<?php

namespace App\Http\Controllers\Admin\Partner;

use App\DataTables\Admin\Partner\CompaniesDataTable;
use App\Http\Controllers\Controller;
use App\Models\PartnerCompany;
use Illuminate\Http\Request;

class PatnerCompanyController extends Controller
{
  public function index(CompaniesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.partner.companies.index');
    // return view('admin.pages.partner.companies.index');
  }
  public function create()
  {
    $data['company'] = new PartnerCompany();
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.companies.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'website' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
    ]);
    PartnerCompany::create($att);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => PartnerCompany::DT_ID, 'close' => 'globalOffCanvas']);
  }

  public function show(PartnerCompany $company)
  {
    //
  }

  public function edit(PartnerCompany $company)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.companies.edit', compact('company'))->render()]);
  }

  public function update(Request $request, PartnerCompany $company)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'website' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
    ]);
    if ($company->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => PartnerCompany::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  public function destroy(PartnerCompany $company)
  {
    if ($company->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => PartnerCompany::DT_ID]);
    }
  }
}
