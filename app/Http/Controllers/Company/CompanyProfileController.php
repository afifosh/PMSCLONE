<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\DetailsUpdateRequest;
use App\Models\CompanyDetail;
use App\Models\Country;
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

    return request()->ajax() ? $this->sendRes('success', ['view_data' =>  view('pages.company-profile.detail.index', $data)->render()])
      // : view('pages.company-profile.edit', $data);
      : view('pages.company-profile.new.edit', $data);

  }

  public function detailedContent()
  {
    $data['countries'] = Country::pluck('name', 'id');

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
    if(auth()->user()->company->detail){
      auth()->user()->company->detail->modifications()->delete();
      auth()->user()->company->detail->updateIfDirty($att);
    }else{
      auth()->user()->company->POCDetail()->delete();
      auth()->user()->company->detail()->create($att);
    }

    return $request->submit_type == 'submit' ? $this->sendRes('Added Successfully', ['event' => 'functionCall', 'function' => 'triggerNext'])
      : $this->sendRes('Saved As Draft', []);
  }

  protected function makeData($request, $fileRepo)
  {
    if ($request->hasFile('logo')) {
      $path = 'company/' . auth()->user()->company->id;
      $logo = $path . '/' . $fileRepo->addAttachment($request->file('logo'), $path);
    }
    $att = isset($logo) ? ['logo' => $logo] + $request->validated() : $request->validated();
    if (!isset($logo))
      $att['logo']= auth()->user()->company->getPOCLogo();
    if (!$request->boolean('is_subsidory'))
      $att['parent_company'] = null;
    if (!$request->boolean('is_parent'))
      $att['subsidiaries'] = null;
    if (!$request->boolean('is_sa_available'))
      $att['sa_company_name'] = null;

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
