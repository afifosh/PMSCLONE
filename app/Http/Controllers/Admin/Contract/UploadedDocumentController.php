<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Company\UsersDataTable;
use App\DataTables\Admin\Contract\DocSignaturesDataTable;
use App\DataTables\Admin\Contract\UploadedDocsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\DocumentUploadRequest;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\KycDocument;
use App\Models\UploadedKycDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadedDocumentController extends Controller
{
  public function index($model, UploadedDocsDataTable $dataTable)
  {
    if (request()->route()->getName() == 'admin.contracts.uploaded-documents.index') {
      $dataTable->model = $data['contract'] =  Contract::findOrFail($model);
    } else {
      $dataTable->model = $data['invoice'] = Invoice::findOrFail($model);
    }

    return $dataTable->render('admin.pages.contracts.uploaded-docs.index', $data);
    // view('admin.pages.contracts.uploaded-docs.index', compact('contract'));
  }

  public function show($model, UploadedKycDoc $uploadedDocument)
  {
    $uploadedDocument->load(['requestedDoc', 'versions' => function ($q) {
      $q->select(['id', 'kyc_doc_id'])->orderBy('id', 'DESC');
    }]);

    $data['document'] = $uploadedDocument->requestedDoc;
    $data['doc'] = $uploadedDocument;

    // if(request()->ajax())
    //   return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.uploaded-docs.show', $data)->render()]);

    if (request()->route()->getName() == 'admin.contracts.uploaded-documents.show') {
      $data['contract'] =  Contract::findOrFail($model);
    } else {
      $data['invoice'] = Invoice::findOrFail($model);
    }

    $data['signaturesTable'] = new DocSignaturesDataTable();
    $data['signaturesTable']->uploadedDoc = $uploadedDocument;

    $data['stampsTable'] = new DocSignaturesDataTable();
    $data['stampsTable']->uploadedDoc = $uploadedDocument;
    $data['stampsTable']->is_signature = false;

    if(request()->get('table') == 'stamps')
      return $data['stampsTable']->render('admin.pages.contracts.uploaded-docs.show-signatures', $data);
    if(request()->get('table') == 'signatures')
      return $data['signaturesTable']->render('admin.pages.contracts.uploaded-docs.show-signatures', $data);


    return view('admin.pages.contracts.uploaded-docs.show-doc', $data);
  }

  public function edit($model, UploadedKycDoc $uploadedDocument)
  {
    if (request()->route()->getName() == 'admin.contracts.uploaded-documents.edit') {
      $data['modelInstance'] = Contract::findOrFail($model);
    } else {
      $data['modelInstance'] = Invoice::findOrFail($model);
    }

    $uploadedDocument->load('requestedDoc');
    $data['document'] = $uploadedDocument->requestedDoc;
    $data['uploaded_doc'] = $uploadedDocument;

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.uploaded-docs.edit', $data)->render(), 'JsMethods' => ['initDropzone']]);
  }

  public function update(DocumentUploadRequest $request, $model, UploadedKycDoc $uploadedDocument)
  {
    if (request()->route()->getName() == 'admin.contracts.uploaded-documents.update') {
      $modelInstance = Contract::findOrFail($model);
    } else {
      $modelInstance = Invoice::findOrFail($model);
    }

    $document = $uploadedDocument->kycDoc;

    if ($document) {
      $final_fields = [];
      $data = [];
      foreach ($document->fields as $field) {
        if ($field['type'] == 'file' && Storage::exists(KycDocument::TEMP_PATH . '/' . $modelInstance::FILES_PATH . '/' . $modelInstance->id . '/' . $request->{'fields.' . $field['id']})) {
          $path = UploadedKycDoc::FILE_PATH . '/' . $modelInstance::FILES_PATH . '/' . $modelInstance->id;
          Storage::move(KycDocument::TEMP_PATH . '/' . $modelInstance::FILES_PATH . '/' . $modelInstance->id . '/' . $request->{'fields.' . $field['id']}, $path . '/' . $request->{'fields.' . $field['id']});
          $field['value'] = $path . '/' . $request->{'fields.' . $field['id']};

          foreach ($uploadedDocument->fields as $uploadedField) {
            if ($uploadedField['id'] == $field['id'] && $uploadedField['type'] == 'file' && $uploadedField['value'] && Storage::exists($uploadedField['value'])) {
              @Storage::delete($uploadedField['value']);
            }
          }
        } else {
          $field['value'] = $request->{'fields.' . $field['id']};
        }
        $final_fields[] = $field;
      }
      if ($document->is_expirable) {
        $data['expiry_date'] = $request->expiry_date;
      } else {
        $data['expiry_date'] = null;
      }
      $data = [
        'uploader_id' => auth()->id(),
        'uploader_type' => get_class(auth()->user()),
        'doc_requestable_type' => get_class($modelInstance),
        'doc_requestable_id' => $modelInstance->id,
      ]
        + $data +
        [
          'fields' => $final_fields,
          'kyc_doc_id' => $document->id
        ];

      $uploadedDocument->update($data);
    }

    return $this->sendRes('Document updated successfully.', ['close' => 'globalModal', 'event' => 'table_reload', 'table_id' => 'uploaded-docs-table']);
  }

  public function destroy($model, UploadedKycDoc $uploadedDocument)
  {
    foreach ($uploadedDocument->fields as $field) {
      if (@$field['type'] == 'file' && $field['value'] && Storage::exists($field['value'])) {
        @Storage::delete($field['value']);
      }
    }

    $uploadedDocument->delete();

    return $this->sendRes('Document deleted successfully.', ['event' => 'table_reload', 'table_id' => 'uploaded-docs-table']);
  }
}
