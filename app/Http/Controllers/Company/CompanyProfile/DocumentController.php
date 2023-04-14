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
  public function index()
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $data['requestable_documents'] = $data['documents'] = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    $data['POC_documents'] = auth()->user()->company->POCKycDoc()->withCount('approvals', 'disapprovals')->get();
    $data['approved_documents'] = auth()->user()->company->kycDocs()->with('modifications.approvals', 'modifications.disapprovals')->get();
    $view_data = auth()->user()->company->isHavingPendingProfile() ? view('pages.company-profile.document.create', $data)->render()
      : view('pages.company-profile.new.detailed-content.documents', $data)->render();

    return $this->sendRes('success', ['view_data' => $view_data]);
  }

  public function store(KycDocumentUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $documents = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    if ($documents->count()) {
      foreach ($documents as $document) {
        $final_fields = [];
        foreach ($document->fields as $i => $field) {
          if ($field['type'] == 'file') {
            $path = CompanyKycDoc::FILE_PATH . '/' . auth()->user()->company_id;
            $doc = $path . '/' . $fileRepository->addAttachment($request->file('doc_' . $document->id . '_field_' . $i . '_' . $field['type']), $path, 'public');
            $final_fields['doc_' . $document->id . '_field_' . $i . '_' . $field['type']] = $doc;
          } else {
            $final_fields['doc_' . $document->id . '_field_' . $i . '_' . $field['type']] = $request->input('doc_' . $document->id . '_field_' . $i . '_' . $field['type']);
          }
        }
        auth()->user()->company->kycDocs()->create(['fields' => $final_fields, 'kyc_doc_id' => $document->id]);
      }
    }

    return $this->sendRes('Added Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }

  public function update($kyc_document, KycDocumentUpdateRequest $request, FileUploadRepository $fileRepository)
  {
    $locality_type = auth()->user()->company->getPOCLocalityType();
    if (!$locality_type) {
      return $this->sendRes('Please update your company profile first', ['event' => 'functionCall', 'function' => 'triggerNext', 'params' => '1']);
    }
    $documents = KycDocument::whereIn('required_from', [3, $locality_type])->where('status', 1)->get();
    if ($documents->count()) {
      foreach ($documents as $document) {
        $final_fields = [];
        foreach ($document->fields as $i => $field) {
          if ($field['type'] == 'file') {
            $path = CompanyKycDoc::FILE_PATH . '/' . auth()->user()->company_id;
            $doc = $path . '/' . $fileRepository->addAttachment($request->file('doc_' . $document->id . '_field_' . $i . '_' . $field['type']), $path, 'public');
            $final_fields['doc_' . $document->id . '_field_' . $i . '_' . $field['type']] = $doc;
          } else {
            $final_fields['doc_' . $document->id . '_field_' . $i . '_' . $field['type']] = $request->input('doc_' . $document->id . '_field_' . $i . '_' . $field['type']);
          }
        }
        if($request->has('doc_id_' . $document->id) && $request->input('doc_id_' . $document->id)){
          auth()->user()->company->kycDocs()->findOrFail($request->input('doc_id_' . $document->id))->modifications()->delete();
          auth()->user()->company->kycDocs()->findOrFail($request->input('doc_id_' . $document->id))->update(['fields' => $final_fields, 'kyc_doc_id' => $document->id]);
        }elseif($request->has('modification_id_' . $document->id) && $request->input('modification_id_' . $document->id)){
          auth()->user()->company->POCKycDoc()->where('is_update', false)->findOrFail($request->input('modification_id_' . $document->id))->delete();
          auth()->user()->company->kycDocs()->create(['fields' => $final_fields, 'kyc_doc_id' => $document->id]);
        }
        auth()->user()->company->kycDocs()->updateOrCreate(['kyc_doc_id' => $document->id], ['fields' => $final_fields]);
      }
    }

    return $this->sendRes('Updated Successfully', ['close' => 'globalModal', 'event' => 'functionCall', 'function' => 'triggerStep', 'function_params' => 5]);
  }
}
