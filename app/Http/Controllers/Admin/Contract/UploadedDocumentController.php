<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\UploadedDocsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\UploadedKycDoc;
use Illuminate\Http\Request;

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

  public function destroy($contract, UploadedKycDoc $uploadedDocument)
  {
    $uploadedDocument->delete();

    return $this->sendRes('Document deleted successfully.', ['event' => 'table_reload', 'table_id' => 'uploaded-docs-table']);
  }
}
