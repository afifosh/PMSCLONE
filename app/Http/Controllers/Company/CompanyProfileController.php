<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\DetailsUpdateRequest;
use App\Models\CompanyDetail;
use App\Models\Country;
use App\Models\KycDocument;
use App\Repositories\FileUploadRepository;

class CompanyProfileController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable:true')->only(['updateDetails']);
  }

  public function editDetails()
  {
    $data['POCDetail'] = auth()->user()->company->POCDetail()->exists() ? auth()->user()->company->POCDetail()->with('approvals', 'disapprovals')->latest()->first() : null;
    $data['detail'] = $data['POCDetail'] ? $this->transformModifications($data['POCDetail']->modifications) : auth()->user()->company->detail()->with('modifications')->first() ?? null;
    $data['countries'] = Country::pluck('name', 'id');
    $data['isHavingPendingProfile'] = auth()->user()->company->isHavingPendingProfile();
    if (!$data['isHavingPendingProfile']) {
      $data['detailsStatus'] = auth()->user()->company->getDetailsStatus();
      $data['contactsStatus'] = auth()->user()->company->getContactsStatus();
      $data['addressesStatus'] = auth()->user()->company->getAddressesStatus();
      $data['accountsStatus'] = auth()->user()->company->getBankAccountsStatus();
      $data['kycDocsStatus'] = auth()->user()->company->getKycDocsStatus();
      $data['stepsApprovedCount'] = auth()->user()->company->getStepApprovedCountAttribute();
    }

    return request()->ajax() ? (auth()->user()->company->isHavingPendingProfile() ? $this->sendRes('success', ['view_data' =>  view('pages.company-profile.detail.index', $data)->render()])
      : $this->sendRes('success', ['view_data' =>  view('pages.company-profile.new.detailed-content.details', $data)->render()]))
      : ($data['isHavingPendingProfile'] ? view('pages.company-profile.edit', $data) : view('pages.company-profile.new.edit', $data));
  }

  public function detailedContent()
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $data['countries'] = Country::pluck('name', 'id');
    $data['POCDetail'] = auth()->user()->company->POCDetail()->exists() ? auth()->user()->company->POCDetail()->with('approvals', 'disapprovals')->latest()->first() : null;
    $data['detail'] = $data['POCDetail'] ? $this->transformModifications($data['POCDetail']->modifications) : auth()->user()->company->detail()->with('modifications')->first() ?? null;
    $data['detailsStatus'] = auth()->user()->company->getDetailsStatus();
    $data['contacts'] = auth()->user()->company->contacts()->with('modifications', 'modifications.disapprovals')->get();
    $data['pending_creation_contacts'] = auth()->user()->company->POCContact()->where('is_update', false)->with('disapprovals')->get();
    $data['addresses'] = auth()->user()->company->addresses;
    $data['pending_addresses'] = auth()->user()->company->POCAddress()->where('is_update', false)->get();
    $data['bankAccounts'] = auth()->user()->company->bankAccounts;
    $data['pending_creation_accounts'] = auth()->user()->company->POCBankAccount()->where('is_update', false)->get();
    $data['requestable_documents'] = $data['documents'] = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    $data['POC_documents'] = auth()->user()->company->POCKycDoc()->withCount('approvals', 'disapprovals')->get();
    $data['approved_documents'] = auth()->user()->company->kycDocs()->with('modifications.approvals', 'modifications.disapprovals')->get();
    $data['isPendingProfile'] = auth()->user()->company->isHavingPendingProfile();
    request()->document_id = request()->document_id ?? $data['requestable_documents'][0]->id;
    // $data['requestable_documents'] = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    // $data['POC_documents'] = auth()->user()->company->POCKycDoc()->withCount('approvals', 'disapprovals')->get();
    // $data['approved_documents'] = auth()->user()->company->kycDocs()->with('modifications.approvals', 'modifications.disapprovals')->get();

    return view('pages.company-profile.new.detailed-content', $data);
  }

  protected function transformModifications($modifications)
  {
    foreach ($modifications as $key => $value) {
      $modifications[$key] = $value['modified'];
    }
    return $modifications;
  }

  public function updateDetails(DetailsUpdateRequest $request, FileUploadRepository $fileRepo)
  {
    $att = $this->makeData($request, $fileRepo);
    if (auth()->user()->company->detail) {
      auth()->user()->company->detail->modifications()->delete();
      auth()->user()->company->detail->updateIfDirty($att);
    } else {
      $detail = auth()->user()->company->POCDetail()->first();
      if ($detail) {
        $detail_mod = transformModifiedData($detail->modifications);
        unset($detail_mod['company_id']);
        // compare both associative arrays of arrays and return the difference
        $diff = array_diff_assoc_recursive($detail_mod, $att);
        if (empty($diff)) {
          return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'Please Make Some Changes']);
        }
        $detail->updateModifications($att);
        // auth()->user()->company->POCDetail()->delete();
      } else {
        auth()->user()->company->detail()->create($att);
      }
    }

    return $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 1]);
  }

  protected function makeData($request, $fileRepo)
  {
    if ($request->hasFile('logo')) {
      $path = 'company/' . auth()->user()->company->id;
      $logo = $path . '/' . $fileRepo->addAttachment($request->file('logo'), $path);
    }
    $att = isset($logo) ? ['logo' => $logo] + $request->validated() : $request->validated();
    if (!isset($logo))
      $att['logo'] = auth()->user()->company->getPOCLogo();
    if (!$request->boolean('is_subsidory'))
      $att['parent_company'] = null;
    if (!$request->boolean('is_parent'))
      $att['subsidiaries'] = null;
    if (!$request->boolean('is_sa_available'))
      $att['sa_company_name'] = null;

    unset($att['submit_type']);

    return $att;
  }

  public function submitApprovalRequest()
  {
    if (auth()->user()->company->canBeSentForApproval()) {
      auth()->user()->company->forceFill(['approval_status' => 2, 'approval_level' => 1])->save();
      return redirect()->back()->with('success', 'Approval Request Submitted Successfully');
    }
    return redirect()->back()->with('error', 'Please fill all the required fields');
  }
}
