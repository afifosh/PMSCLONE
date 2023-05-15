<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\ApprovalHistoryRequestsDataTable;
use App\DataTables\Admin\Company\ApprovalRequestsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyProfile\ApprovalUpdateRequest;
use App\Models\ApprovalLevel;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyBankAccount;
use App\Models\CompanyContact;
use App\Models\CompanyDetail;
use App\Models\CompanyKycDoc;
use App\Models\Country;
use App\Models\KycDocument;

class ApprovalRequestController extends Controller
{
  public function index(ApprovalRequestsDataTable $dataTable)
  {
    if (request()->route()->getName() == 'admin.change-requests.index') {
      $dataTable->type = 'change';
      $title = 'Change Requests';
    } elseif (request()->route()->getName() == 'admin.approval-requests.index') {
      $dataTable->type = 'approval';
      $title = 'Approval Requests';
    } elseif (request()->route()->getName() == 'admin.pending-companies.index') {
      $dataTable->type = 'pending';
      $title = 'Pending Approval Companies';
    } elseif (request()->route()->getName() == 'admin.verified-companies.index') {
      $dataTable->type = 'verified';
      $title = 'Verified Companies';
    }
    $levels = ApprovalLevel::pluck('name', 'id');
    $type = $dataTable->type;
    return $dataTable->render('admin.pages.company.approval-request.index', compact('levels', 'title', 'type'));
    // view('admin.pages.company.approval-request.index');
  }

  public function getCompanyReqeust($level, Company $company)
  {
    if(!$company->approvalRequests()->where('status', 0)->exists() || $level != $company->approval_level){
      return back()->with('error', 'Request Not found');
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['company'] = $company;
    $data['level'] = $level;
    $data['detailsStatus'] = $company->getDetailsStatus($level);
    $data['contactsStatus'] = $company->getContactsStatus($level);
    $data['addressesStatus'] = $company->getAddressesStatus($level);
    $data['accountsStatus'] = $company->getBankAccountsStatus($level);
    $data['kycDocStatus'] = $company->getKycDocsStatus($level);
    $data['overAllStatus'] = $company->getOverallStatus($level);
    if (request()->tab == 'details' || request()->tab == null) {
      request()->tab = 'details';
      $data['legalForms'] = CompanyDetail::LegalForms;
      $data['localityTypes'] = CompanyDetail::LocalityTypes;
      $data['NoOfEmployee'] = CompanyDetail::NoOfEmployee;
      // $data['detail'] = $company->POCDetail()->count() ? $company->POCDetail()->withCount('approvals', 'disapprovals')->first() : $company->detail;
      $data['POCDetail'] = $company->POCDetail()->exists() ? $company->POCDetail()->with('approvals.approver', 'disapprovals.disapprover')->latest()->first() : null;
      if($data['POCDetail']){
        $data['detail'] = transformModifiedData($data['POCDetail']->modifications);
        $data['detail']['modification_id'] = $data['POCDetail']->id;
      }else{
        $data['detail'] = $company->detail()->with('modifications.approvals.approver', 'modifications.disapprovals.disapprover')->first() ?? null;
      }
      // $data['detail'] = $data['POCDetail'] ? transformModifiedData($data['POCDetail']->modifications) : $company->detail()->with('modifications')->first() ?? null;
      $data['fields'] = CompanyDetail::getFields();
    } elseif (request()->tab == 'contact-persons') {
      $data['contactTypes'] = CompanyContact::getContactTypes();
      $data['contacts'] = $company->POCCOntact()->with('approvals.approver', 'disapprovals.disapprover')->withCount('approvals', 'disapprovals')->get();
      $data['approved_contacts'] = $company->contacts()->with('modifications.approvals.approver', 'modifications.disapprovals.disapprover')->get();
      $data['fields'] = CompanyContact::getFields();
    } elseif (request()->tab == 'addresses') {
      $data['addressTypes'] = CompanyAddress::getAddressTypes();
      $data['addresses'] = $company->POCAddress()->with('approvals.approver', 'disapprovals.disapprover')->withCount('approvals', 'disapprovals')->get();
      $data['approved_addresses'] = $company->addresses()->with('modifications.approvals.approver', 'modifications.disapprovals.disapprover')->get();
      $data['fields'] = CompanyAddress::getFields();
    } elseif (request()->tab == 'bank-accounts') {
      $data['bankAccounts'] = $company->POCBankAccount()->with('approvals.approver', 'disapprovals.disapprover')->withCount('approvals', 'disapprovals')->get();
      $data['approved_bank_accounts'] = $company->bankAccounts()->with('modifications.approvals.approver', 'modifications.disapprovals.disapprover')->get();
      $data['fields'] = CompanyBankAccount::getFields();
    } elseif (request()->tab == 'documents') {
      $data['documents'] = $company->POCKycDoc()->with('approvals.approver', 'disapprovals.disapprover')->withCount('approvals', 'disapprovals')->get();
      $data['approved_documents'] = $company->kycDocs()->with('modifications.approvals.approver', 'modifications.disapprovals.disapprover')->get();
      $data['requestedDocs'] = KycDocument::whereIn('required_from', [3, $company->getPOCLocalityType()])->where('status', 1)->get();
      $data['docModel'] = new CompanyKycDoc ();
    }

    return view('admin.pages.company.approval-request.vertical.show', $data);
    return view('admin.pages.company.approval-request.show', $data);
  }

  public function updateApprovalRequest($level, Company $company, ApprovalUpdateRequest $request)
  {
    foreach (array_unique($request->modification_ids) as $modification_id) {
      $mod = $company->POCmodifications()->whereId($modification_id)->first();
      abort_if($level != $company->approval_level || !$mod, 404);
      if(!$mod->isApprovable($level))
         return $this->sendErr('Unauthorized');
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
    return  $company->isApprovalRequiredForCurrentLevel($level) ? $this->sendRes($message, ['event' => 'page_reload',])
      : ($company->verified_at ? $this->sendRes('Saved Successfully', ['event' => 'redirect', 'url' => route('admin.verified-companies.index')])
        : $this->sendRes('Saved Successfully', ['event' => 'redirect', 'url' => route('admin.approval-requests.index')]));
  }

  public function indexHistory(ApprovalHistoryRequestsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.company.approval-request.indexHistory');
    // return view('admin.pages.company.approval-request.indexHistory');
  }
}
