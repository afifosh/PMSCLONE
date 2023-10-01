<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\UploadedDocsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\DocumentUploadRequest;
use App\Models\Contract;
use App\Models\KycDocument;
use App\Models\UploadedKycDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadedDocumentController extends Controller
{
  public function index(Contract $contract, UploadedDocsDataTable $dataTable)
  {
    $dataTable->contract = $contract;

    $data['contract'] = $contract;
    return $dataTable->render('admin.pages.contracts.uploaded-docs.index', $data);
    // view('admin.pages.contracts.uploaded-docs.index', compact('contract'));
  }

  public function show(Contract $contract, UploadedKycDoc $uploadedDocument)
  {
    $data['contract'] = $contract;
    $uploadedDocument->load('requestedDoc');
    $data['document'] = $uploadedDocument->requestedDoc;
    $data['doc'] = $uploadedDocument;

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.uploaded-docs.show', $data)->render()]);
  }

  public function edit(Contract $contract, UploadedKycDoc $uploadedDocument)
  {
    $data['contract'] = $contract;
    $uploadedDocument->load('requestedDoc');
    $data['document'] = $uploadedDocument->requestedDoc;
    $data['uploaded_doc'] = $uploadedDocument;

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.uploaded-docs.edit', $data)->render(), 'JsMethods' => ['initDropzone']]);
  }

  public function update(DocumentUploadRequest $request, Contract $contract, UploadedKycDoc $uploadedDocument)
  {
    $document = $contract->requestedDocs()->findOrFail($request->document_id);

    if ($document) {
      $final_fields = [];
      $data = [];
      foreach ($document->fields as $field) {
        if ($field['type'] == 'file' && Storage::exists(KycDocument::TEMP_PATH . '/contracts/' . $contract->id . '/' . $request->{'fields.' . $field['id']})) {
          $path = UploadedKycDoc::FILE_PATH . '/contracts/' . $contract->id;
          Storage::move(KycDocument::TEMP_PATH . '/contracts/' . $contract->id . '/' . $request->{'fields.' . $field['id']}, $path . '/' . $request->{'fields.' . $field['id']});
          $field['value'] = $path . '/' . $request->{'fields.' . $field['id']};

          foreach($uploadedDocument->fields as $uploadedField) {
            if($uploadedField['id'] == $field['id'] && $uploadedField['type'] == 'file' && $uploadedField['value'] && Storage::exists($uploadedField['value'])) {
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
        'doc_requestable_type' => get_class($contract),
        'doc_requestable_id' => $contract->id,
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

  public function destroy(Contract $contract, UploadedKycDoc $uploadedDocument)
  {
    foreach($uploadedDocument->fields as $field) {
      if(@$field['type'] == 'file' && $field['value'] && Storage::exists($field['value'])) {
        @Storage::delete($field['value']);
      }
    }

    $uploadedDocument->delete();

    return $this->sendRes('Document deleted successfully.', ['event' => 'table_reload', 'table_id' => 'uploaded-docs-table']);
  }
}
