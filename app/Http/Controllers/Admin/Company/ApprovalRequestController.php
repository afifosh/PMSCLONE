<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\ApprovalRequestsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyProfile\ApprovalUpdateRequest;
use App\Models\ApprovalLevel;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyBankAccount;
use App\Models\CompanyContact;
use App\Models\CompanyDetail;
use App\Models\Country;

class ApprovalRequestController extends Controller
{
  public function index(ApprovalRequestsDataTable $dataTable)
  {
    if(request()->route()->getName() == 'admin.change-requests.index'){
      $dataTable->type = 'change';
      $title = 'Change Requests';
    }elseif(request()->route()->getName() == 'admin.approval-requests.index'){
      $dataTable->type = 'approval';
      $title = 'Approval Requests';
    }elseif(request()->route()->getName() == 'admin.pending-companies.index'){
      $dataTable->type = 'pending';
      $title = 'Pending Approval Companies';
    }
    $levels = ApprovalLevel::pluck('name', 'id');
    return $dataTable->render('admin.pages.company.approval-request.index', compact('levels', 'title'));
    // view('admin.pages.company.approval-request.index');
  }

  public function getCompanyReqeust($level, Company $company)
  {
    $data['detail'] = $company->POCDetail()->count() ? $this->transformModificationsModel($company->POCDetail()->first()) : $company->detail;
    $data['contacts'] = $this->transformModificationsModel($company->POCCOntact()->get());
    $data['addresses'] = $this->transformModificationsModel($company->POCAddress()->get());
    $data['bankAccounts'] = $this->transformModificationsModel($company->POCBankAccount()->get());
    $data['countries'] = Country::pluck('name', 'id');
    $data['company'] = $company;
    if(request()->tab == 'details' || request()->tab == null){
      request()->tab = 'details';
      $data['fields'] = CompanyDetail::getFields();
    }elseif(request()->tab == 'contact-persons'){
      // $data['fields'] = CompanyContact::getFields();
      $data['fields'] = CompanyDetail::getFields();
    }elseif(request()->tab == 'addresses'){
      $data['fields'] = CompanyAddress::getFields();
    }elseif(request()->tab == 'bank-accounts'){
      $data['fields'] = CompanyBankAccount::getFields();
    }elseif(request()->tab == 'documents'){
      $data['fields'] = [];
    }

    return view('admin.pages.company.approval-request.vertical.show', $data);
    return view('admin.pages.company.approval-request.show', $data);
  }

  public function transformModificationsModel($model)
  {
    // check if the model is a collection or a single model
    if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
      $modifications = [];
      foreach ($model as $key => $value) {
        $modifications[$key] = $this->transformModifications($value->modifications);
        $modifications[$key]['modification_id'] = $value->id;
      }
      return $modifications;
    } else {
      return ['modification_id' => $model->id] + $this->transformModifications($model->modifications);
    }
  }

  protected function transformModifications($modifications)
  {
    foreach ($modifications as $key => $value) {
      $modifications[$key] = $value['modified'];
    }
    return $modifications;
  }

  public function updateApprovalRequest($level, Company $company, ApprovalUpdateRequest $request)
  {
    foreach (array_unique($request->modification_ids) as $modification_id) {
      $mod = $company->POCmodifications()->whereId($modification_id)->first();
      abort_if($level != $company->approval_level || !$mod, 404);
      if ($request->boolean('approval_status.' . $modification_id)) {
        auth()->user()->approve($mod);
      } else {
        auth()->user()->disapprove($mod, $request->disapproval_reason[$modification_id]);
        $company->forceFill(['approval_status' => 3, 'approval_level' => 1]);
        $company->save();
      }
      $company->incApprovalLevelIfRequired();
    }
    $company->refresh();
    return  $company->isApprovalRequiredForCurrentLevel($level) ? $this->sendRes('Updated Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved Successfully', ['event' => 'redirect', 'url' => route('admin.approval-requests.index')]);
  }
}
