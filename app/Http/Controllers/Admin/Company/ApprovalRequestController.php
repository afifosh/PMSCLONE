<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\CompaniesDataTable;
use App\DataTables\Admin\Company\ApprovalRequestsDataTable;
use App\Http\Controllers\Controller;
use App\Models\ApprovalLevel;
use App\Models\Company;
use App\Models\Country;
use Illuminate\Http\Request;

class ApprovalRequestController extends Controller
{
  public function index(ApprovalRequestsDataTable $dataTable)
  {
    $levels = ApprovalLevel::pluck('name', 'id');
    return $dataTable->render('admin.pages.company.approval-request.index', compact('levels'));
    // view('admin.pages.company.approval-request.index');
  }

  public function getCompanyReqeust($level, Company $company)
  {
    $data['detail'] = $this->transformModificationsModel($company->POCDetail()->first());
    $data['contacts'] = $this->transformModificationsModel($company->POCCOntact()->get());
    $data['addresses'] = $this->transformModificationsModel($company->POCAddress()->get());
    $data['bankAccounts'] = $this->transformModificationsModel($company->POCBankAccount()->get());
    $data['countries'] = Country::pluck('name', 'id');
    $data['company'] = $company;
    return view('admin.pages.company.approval-request.show', $data);
  }

  public function transformModificationsModel($model)
  {
    // check if the model is a collection or a single model
    if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
      $modifications = [];
      foreach ($model as $key => $value) {
        $modifications[$key] = $this->transformModifications($value->modifications);
      }
      return $modifications;
    } else {
      return $this->transformModifications($model->modifications);
    }
  }

  protected function transformModifications($modifications)
  {
    foreach ($modifications as $key => $value) {
      $modifications[$key] = $value['modified'];
    }
    return $modifications;
  }
}
