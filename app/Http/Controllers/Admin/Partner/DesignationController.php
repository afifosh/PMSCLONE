<?php

namespace App\Http\Controllers\Admin\Partner;

use App\DataTables\Admin\Partner\DesignationsDataTable;
use App\Http\Controllers\Controller;
use App\Models\CompanyDepartment;
use App\Models\CompanyDesignation;
use App\Models\PartnerCompany;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
  public function index(DesignationsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.partner.designations.index');
    // return view('admin.pages.partner.designations.index');
  }
  public function create()
  {
    $data['designation'] = new CompanyDesignation();
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend(__('Select Company'), '');
    $data['departments'] = ['' => __('Select Department')];
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.designations.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'company' => 'required|exists:partner_companies,id',
      'department' => 'required|exists:company_departments,id',
    ]);
    CompanyDesignation::create(['name' => $att['name'], 'department_id' => $att['department']]);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => CompanyDesignation::DT_ID, 'close' => 'globalOffCanvas']);
  }

  public function show(CompanyDesignation $designation)
  {
    //
  }

  public function edit(CompanyDesignation $designation)
  {
    $data['designation'] = $designation;
    $data['companies'] = PartnerCompany::pluck('name', 'id');
    $data['departments'] = CompanyDepartment::where('company_id', $designation->department->company_id)->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.designations.edit', $data)->render()]);
  }

  public function update(Request $request, CompanyDesignation $designation)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'company' => 'required|exists:partner_companies,id',
      'department' => 'nullable|exists:company_departments,id',
    ]);
    if ($designation->update(['name' => $att['name'], 'department_id' => $att['department']])) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => CompanyDesignation::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  public function destroy(CompanyDesignation $designation)
  {
    if ($designation->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => CompanyDesignation::DT_ID]);
    }
  }

  public function getByDepartmentId(Request $request)
  {
    $data = CompanyDesignation::where('department_id', $request->id)->pluck('name', 'id')->prepend('Select Designation', '');
    return $this->sendRes(__('Designations List'), ['data' => $data]);
  }
}
