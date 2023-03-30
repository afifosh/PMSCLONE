<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\KycDocumentUpdateRequest;
use App\Models\KycDocument;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
  public function index()
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if(!$locality_type){
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $documents = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();

    return $this->sendRes('success', ['view_data' => view('pages.company-profile.document.create', compact('documents'))->render()]);
  }

  public function store(KycDocumentUpdateRequest $request)
  {
    dd($request->all());
  }
}
