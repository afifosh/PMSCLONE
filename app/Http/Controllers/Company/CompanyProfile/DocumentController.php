<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\KycDocumentUpdateRequest;
use App\Models\CompanyKycDoc;
use App\Models\KycDocument;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
  public function index(Request $request)
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $data['requestable_documents'] = $data['documents'] = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    $data['POC_documents'] = auth()->user()->company->POCKycDoc()->withCount('approvals', 'disapprovals')->get();
    $data['approved_documents'] = auth()->user()->company->kycDocs()->with('modifications.approvals', 'modifications.disapprovals')->get();
    $data['isPendingProfile'] = auth()->user()->company->isHavingPendingProfile();
    request()->document_id = request()->document_id ?? $data['requestable_documents'][0]->id;
    $view_data = $data['isPendingProfile'] ? view('pages.company-profile.document.create', $data)->render()
      : view('pages.company-profile.new.detailed-content.documents', $data)->render();

    return $this->sendRes('success', ['view_data' => $view_data]);
  }

  public function store(KycDocumentUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $document = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->findOrFail($request->document_id);
    $final_fields = [];
    $data = [];
    foreach ($document->fields as $field) {
      if ($field['type'] == 'file') {
        $path = CompanyKycDoc::FILE_PATH . '/' . auth()->user()->company_id;
        $field['value'] = $path . '/' . $fileRepository->addAttachment($request->file('fields.'.$field['id']), $path, 'public');
      } else {
        $field['value'] = $request->{'fields.' . $field['id']};
      }
      $final_fields[] = $field;
    }
    if($document->is_expirable){
      $data['expiry_date'] = $request->expiry_date;
    }
    auth()->user()->company->kycDocs()->create($data + ['fields' => $final_fields, 'kyc_doc_id' => $document->id]);

    return $this->triggerNextDoc('Added Successfully', $locality_type, $request);
    // return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function update($kyc_document, KycDocumentUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $document = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->findOrFail($request->document_id);
    if ($document) {
      $final_fields = [];
      $data = [];
      foreach ($document->fields as $i => $field) {
        if ($field['type'] == 'file') {
          $path = CompanyKycDoc::FILE_PATH . '/' . auth()->user()->company_id;
          $field['value'] = $path . '/' . $fileRepository->addAttachment($request->file('fields.'.$field['id']), $path, 'public');
        } else {
          $field['value'] = $request->{'fields.' . $field['id']};
        }
      }
      $final_fields[] = $field;
      if($document->is_expirable){
        $data['expiry_date'] = $request->expiry_date;
      }
      if($request->has('doc_id_' . $document->id) && $request->input('doc_id_' . $document->id)){
        auth()->user()->company->kycDocs()->findOrFail($request->input('doc_id_' . $document->id))->modifications()->delete();
        auth()->user()->company->kycDocs()->findOrFail($request->input('doc_id_' . $document->id))->update($data + ['fields' => $final_fields, 'kyc_doc_id' => $document->id]);
      }elseif($request->has('modification_id_' . $document->id) && $request->input('modification_id_' . $document->id)){
        auth()->user()->company->POCKycDoc()->where('is_update', false)->findOrFail($request->input('modification_id_' . $document->id))->delete();
        auth()->user()->company->kycDocs()->create($data + ['fields' => $final_fields, 'kyc_doc_id' => $document->id]);
      }
    }

    return $this->triggerNextDoc('Updated Successfully', $locality_type, $request);
    // return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  protected function triggerNextDoc($message, $locality_type, $request)
  {
    $documents = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->where('id', '!=', $request->document_id)->get();
    return $this->sendRes($message, ['event' => 'functionCall', 'function' => 'triggerNextDoc', 'params' => @$documents[0]->id ?? 0]);
  }
}
