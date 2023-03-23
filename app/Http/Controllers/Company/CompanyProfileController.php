<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\DetailsUpdateRequest;
use App\Models\CompanyDetail;
use App\Models\Country;
use App\Repositories\FileUploadRepository;

class CompanyProfileController extends Controller
{
  public function editDetails()
  {
    $data['detail'] = auth()->user()->company->POCDetail()->first() ? $this->transformModifications(auth()->user()->company->POCDetail()->first()->modifications) : auth()->user()->company->detail ?? new CompanyDetail;
    $data['countries'] = Country::pluck('name', 'id');

    return request()->ajax() ? $this->sendRes('success', ['view_data' =>  view('pages.company-profile.detail.index', $data)->render()])
      : view('pages.company-profile.edit', $data);
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
    if ($request->submit_type == 'submit') {
      auth()->user()->company->detail()->updateOrCreate(['company_id' => auth()->user()->company_id], $att);
      auth()->user()->company->draftDetail()->where('type', 'detail')->delete();
    } else {
      auth()->user()->company->draftDetail()->updateOrCreate(['type' => 'detail'], ['data' => $att]);
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
      unset($att['logo']);
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
