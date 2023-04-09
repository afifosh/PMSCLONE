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
    $data['countries'] = Country::pluck('name', 'id');
    $data['company'] = $company;
    $data['level'] = $level;
    $data['detailsStatus'] = $company->getDetailsStatus($level);
    $data['contactsStatus'] = $company->getContactsStatus($level);
    $data['addressesStatus'] = $company->getAddressesStatus($level);
    $data['accountsStatus'] = $company->getBankAccountsStatus($level);
    if(request()->tab == 'details' || request()->tab == null){
      request()->tab = 'details';
      $data['detail'] = $company->POCDetail()->count() ? $company->POCDetail()->withCount('approvals', 'disapprovals')->first() : $company->detail;
      $data['fields'] = CompanyDetail::getFields();
    }elseif(request()->tab == 'contact-persons'){
      $data['contacts'] = $company->POCCOntact()->withCount('approvals', 'disapprovals')->get();
      $data['approved_contacts'] = $company->contacts()->get();
      $data['fields'] = CompanyContact::getFields();
    }elseif(request()->tab == 'addresses'){
      $data['addresses'] = $company->POCAddress()->withCount('approvals', 'disapprovals')->get();
      $data['approved_addresses'] = $company->addresses()->get();
      $data['fields'] = CompanyAddress::getFields();
    }elseif(request()->tab == 'bank-accounts'){
      $data['bankAccounts'] = $company->POCBankAccount()->withCount('approvals', 'disapprovals')->get();
      $data['approved_bank_accounts'] = $company->bankAccounts()->get();
      $data['fields'] = CompanyBankAccount::getFields();
    }elseif(request()->tab == 'documents'){
      $data['fields'] = [];
    }

    return view('admin.pages.company.approval-request.vertical.show', $data);
    return view('admin.pages.company.approval-request.show', $data);
  }

  public function updateApprovalRequest($level, Company $company, ApprovalUpdateRequest $request)
  {
    foreach (array_unique($request->modification_ids) as $modification_id) {
      $mod = $company->POCmodifications()->whereId($modification_id)->first();
      abort_if($level != $company->approval_level || !$mod, 404);
      if ($request->boolean('approval_status.' . $modification_id)) {
        auth()->user()->approve($mod, @$request->comment[$modification_id]);
        $message = 'Approval Successfull';
      } else {
        auth()->user()->disapprove($mod, @$request->comment[$modification_id]);
        $message = 'Request Rejected';
      }
      $company->incApprovalLevelIfRequired();
    }
    $company->refresh();
    return  $company->isApprovalRequiredForCurrentLevel($level) ? $this->sendRes($message, ['event' => 'page_reload', ])
    : $this->sendRes('Saved Successfully', ['event' => 'redirect', 'url' => route('admin.approval-requests.index')]);
  }
}
