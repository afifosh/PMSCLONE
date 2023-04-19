<?php

namespace App\Http\Controllers\Company\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyProfile\KycDocumentUpdateRequest;
use App\Models\CompanyKycDoc;
use App\Models\KycDocument;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Throwable;

class DocumentController extends Controller
{
  public function __construct()
  {
    $this->middleware('companyMustBeEditable:true')->except(['index', 'show']);
  }

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
    if($request->fields_only){
      $data['document'] = $data['documents']->where('id', $request->document_id)->first();
      $view_data = view('pages.company-profile.document.fields', $data)->render();
    }else{
      $view_data = view('pages.company-profile.document.create', $data)->render();
    }
      // : view('pages.company-profile.new.detailed-content.documents', $data)->render();

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
        Storage::move(KycDocument::TEMP_PATH.'/'.auth()->user()->company_id.'/'.$request->{'fields.' . $field['id']}, $path . '/'.$request->{'fields.' . $field['id']});
        $field['value'] = $path . '/' . $request->{'fields.' . $field['id']};
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
      foreach ($document->fields as $field) {
        if ($field['type'] == 'file') {
          $path = CompanyKycDoc::FILE_PATH . '/' . auth()->user()->company_id;
          Storage::move(KycDocument::TEMP_PATH.'/'.auth()->user()->company_id.'/'.$request->{'fields.' . $field['id']}, $path . '/'.$request->{'fields.' . $field['id']});
          $field['value'] = $path . '/' . $request->{'fields.' . $field['id']};
        } else {
          $field['value'] = $request->{'fields.' . $field['id']};
        }
        $final_fields[] = $field;
      }
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

    return $this->sendRes($message, ['event' => 'functionCall', 'function' => 'triggerNextDoc', 'function_params' => @$documents[0]->id ?? -1]);
  }

  public function uploadDocument(Request $request, FileUploadRepository $file_repo){
    $request->validate([
      'file' => 'required|mimetypes:text/plain,application/*,image/*,video/*,audio/*'
    ]);

    $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
    // check if the upload is success, throw exception or return response you need
    if ($receiver->isUploaded() === false) {
      throw new UploadMissingFileException();
    }
    // receive the file
    $save = $receiver->receive();

    // check if the upload has finished (in chunk mode it will send smaller files)
    if ($save->isFinished()) {
      // save the file and return any response you need, current example uses `move` function. If you are
      // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
      return $this->saveFile($save->getFile(), $file_repo);
    }

    // we are in chunk mode, lets send the current progress
    /** @var AbstractHandler $handler */
    $handler = $save->handler();

    return response()->json([
      "done" => $handler->getPercentageDone(),
      'status' => true
    ]);
  }

  public function saveFile($file, FileUploadRepository $file_repo)
  {
    $path = KycDocument::TEMP_PATH . DIRECTORY_SEPARATOR . auth()->user()->company_id;
    $file_path = $file_repo->addAttachment($file, $path);

    return $this->sendRes('Uploaded Successfully', ['file_path' => $file_path]);
  }
}
