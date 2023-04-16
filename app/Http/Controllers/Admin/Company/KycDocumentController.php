<?php

namespace App\Http\Controllers\Admin\Company;

use App\DataTables\Admin\Company\KycDocumentsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KycDocumentUpdateRequest;
use App\Models\KycDocument;

class KycDocumentController extends Controller
{
  public function index(KycDocumentsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.kycdocument.index');
  }

  public function create()
  {
    $types = KycDocument::TYPES;
    $kyc_document = new KycDocument();

    return view('admin.pages.kycdocument.create', compact('types', 'kyc_document'));
  }

  public function store(KycDocumentUpdateRequest $request)
  {
    $data['fields'] = $this->addIDs($request->fields);
    $data['is_expirable'] = $request->boolean('is_expirable');
    KycDocument::create($data + $request->validated());

    return $this->sendRes(__('Kyc Document Added Successfully'), ['event' => 'redirect', 'url' => route('admin.kyc-documents.index')]);
  }

  public function edit(KycDocument $kyc_document)
  {
    $types = KycDocument::TYPES;

    return view('admin.pages.kycdocument.create', compact('kyc_document', 'types'));
  }

  public function update(KycDocumentUpdateRequest $request, KycDocument $kyc_document)
  {
    $data['fields'] = $this->addIDs($request->fields);
    $data['is_expirable'] = $request->boolean('is_expirable');
    $kyc_document->update($data + $request->validated());

    return $this->sendRes(__('Kyc Document Updated Successfully'), ['event' => 'redirect', 'url' => route('admin.kyc-documents.index')]);
  }

  protected function addIDs($fields)
  {
    foreach ($fields as $key => $field) {
      $fields[$key]['id'] = @$fields[$key]['id'] ?? uniqid();
    }

    return $fields;
  }
}
