<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\DocumentUploadRequest;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\KycDocument;
use App\Models\UploadedKycDoc;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ContractDocumentController extends Controller
{
  public function index($modal, Request $request)
  {
    if ($request->route()->getName() == 'admin.contracts.pending-documents.index') {
      $data['contract'] = $contract = Contract::findOrFail($modal);
      $data['contract'] = $contract;
      $data['requestable_documents'] = $data['documents'] = $contract->requestedDocs()->get();
      $data['valid_documents'] = $contract->uploadedValidDocs()->pluck('id')->toArray();
      $data['expired_documents'] = $contract->uploadedExpiredDocs()->pluck('id')->toArray();
    } else {
      $data['invoice'] = $invoice = Invoice::findOrFail($modal);
      $data['requestable_documents'] = $data['documents'] = $invoice->requestedDocs()->get();
      $data['valid_documents'] = $invoice->uploadedValidDocs()->pluck('id')->toArray();
      $data['expired_documents'] = $invoice->uploadedExpiredDocs()->pluck('id')->toArray();
    }

    request()->document_id = request()->document_id ?? $data['requestable_documents'][0]->id ?? 0;

    if (in_array(request()->document_id, $data['valid_documents'])){
      // update if document is valid
      $data['uploaded_doc'] = UploadedKycDoc::where('kyc_doc_id', request()->document_id)
        ->orderBy('id', 'DESC')
        ->first();
      if($request->route()->getName() == 'admin.contracts.pending-documents.index'){
        $data['modelInstance'] = $contract;
      }else{
        $data['modelInstance'] = $invoice;
      }
      request()->merge([
        'success' => 'page_reload',
      ]);
    }


    if ($request->fields_only) {
      $data['document'] = $data['documents']->where('id', $request->document_id)->first();

      if (in_array(request()->document_id, $data['valid_documents'])){
        return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.uploaded-docs.edit', $data)->render()]);
      }
      return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.pending-documents.fields', $data)->render()]);
    } else {

      return view('admin.pages.contracts.pending-documents.create', $data);
    }
  }

  public function store(DocumentUploadRequest $request, $modal)
  {
    if ($request->route()->getName() == 'admin.contracts.pending-documents.store') {
      $modelInstance = Contract::findOrFail($modal);
    } else {
      $modelInstance = Invoice::findOrFail($modal);
    }

    $document = $modelInstance->pendingDocs()->findOrFail($request->document_id);
    $final_fields = [];
    $data = [];
    foreach ($document->fields as $field) {
      if ($field['type'] == 'file') {
        $path = UploadedKycDoc::FILE_PATH . '/' . $modelInstance::FILES_PATH . '/' . $modal;
        Storage::move(KycDocument::TEMP_PATH . '/'. $modelInstance::FILES_PATH .'/' . $modal . '/' . $request->{'fields.' . $field['id']}, $path . '/' . $request->{'fields.' . $field['id']});
        $field['value'] = $path . '/' . $request->{'fields.' . $field['id']};
      } else {
        $field['value'] = $request->{'fields.' . $field['id']};
      }
      $final_fields[] = $field;
    }
    if ($document->is_expirable) {
      $data['expiry_date'] = $request->expiry_date;
    }

    if ($document->having_refrence_id) {
      $data['refrence_id'] = $request->refrence_id;
    }
    $modelInstance->uploadedDocs()->create(
      [
        'uploader_id' => auth()->id(),
        'uploader_type' => get_class(auth()->user())
      ]
        + $data +
        [
          'fields' => $final_fields,
          'kyc_doc_id' => $document->id
        ]
    );

    return $this->sendRes('Added Successfully', ['event' => 'page_reload']);
  }

  public function uploadDocument($modal, Request $request, FileUploadRepository $file_repo)
  {
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
      return $this->saveFile($save->getFile(), $file_repo, $modal);
    }

    // we are in chunk mode, lets send the current progress
    /** @var AbstractHandler $handler */
    $handler = $save->handler();

    return response()->json([
      "done" => $handler->getPercentageDone(),
      'status' => true
    ]);
  }

  public function saveFile($file, FileUploadRepository $file_repo, $modal)
  {
    if(request()->route()->getName() == 'admin.contracts.upload-requested-doc')
      $prefix = 'contracts';
    else
      $prefix = 'invoices';

    $path = KycDocument::TEMP_PATH . DIRECTORY_SEPARATOR . $prefix . '/' . $modal;
    $file_path = $file_repo->addAttachment($file, $path);

    return $this->sendRes('Uploaded Successfully', ['file_path' => $file_path]);
  }
}
