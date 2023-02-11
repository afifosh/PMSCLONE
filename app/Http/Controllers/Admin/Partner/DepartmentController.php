<?php

namespace App\Http\Controllers\Admin\Partner;

use App\DataTables\Admin\Partner\DepartmentsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CompanyDepartment;
use App\Models\PartnerCompany;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
  public function index(DepartmentsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.partner.departments.index');
    // return view('admin.pages.partner.departments.index');
  }
  public function create()
  {
    $data['department'] = new CompanyDepartment();
    $data['admins'] = Admin::pluck('email', 'id')->prepend('Select Head', '');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend('Select Organization', '');
    $data['departments'] = ['' => 'Select Department'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.departments.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', Rule::unique('company_departments')->where(function ($query) use ($request) {
        return $query->whereName($request->name)->where('company_id',$request->company);
      }),],
      'company' => ['required', 'exists:partner_companies,id'],
      'head' => 'nullable|exists:admins,id',
    ]);
    CompanyDepartment::create(['name' => $att['name'], 'company_id' => $att['company'], 'head_id' => $att['head']]);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => CompanyDepartment::DT_ID, 'close' => 'globalOffCanvas']);
  }

  public function show(CompanyDepartment $department)
  {
    //
  }

  public function edit(CompanyDepartment $department)
  {
    $data['department'] = $department;
    $data['admins'] = Admin::pluck('email', 'id')->prepend('Select Head', '');
    $data['companies'] = PartnerCompany::pluck('name', 'id');
    $data['departments'] = ['' => 'Select Department'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.partner.departments.edit', $data)->render()]);
  }

  public function update(Request $request, CompanyDepartment $department)
  {
    $att = $request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('company_departments')->where(function ($query) use ($request) {
        return $query->whereName($request->name)->where('company_id',$request->company);
      })->ignore($department->id),],
      'company' => 'required|exists:partner_companies,id',
      'head' => 'nullable|exists:admins,id',
    ]);
    if ($department->update(['name' => $att['name'], 'company_id' => $att['company'], 'head_id' => $att['head']])) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => CompanyDepartment::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  public function destroy(CompanyDepartment $department)
  {
    if ($department->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => CompanyDepartment::DT_ID]);
    }
  }

  public function getByComapnyId(Request $request)
  {
    $data = CompanyDepartment::where('company_id', $request->id)->pluck('name', 'id')->prepend('Select Department' , '');
    return $this->sendRes('Departments list', ['data' => $data]);
  }
}
