<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\DetailsUpdateRequest;
use App\Models\CompanyDetail;
use App\Models\Country;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
  public function editDetails()
  {
    $data['detail'] = auth()->user()->company->draftDetail ? auth()->user()->company->draftDetail->data :  auth()->user()->company->detail ?? new CompanyDetail;
    $data['form'] = 'company-details';
    $data['countries'] = Country::pluck('name', 'id');
    return view('pages.company-profile.edit', $data);
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
}
