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
    KycDocument::create($request->only(['title', 'status', 'required_from', 'fields']));

    return $this->sendRes(__('Kyc Document Added Successfully'), ['event' => 'redirect', 'url' => route('admin.kyc-documents.index')]);
  }

  public function edit(KycDocument $kyc_document)
  {
    $types = KycDocument::TYPES;

    return view('admin.pages.kycdocument.create', compact('kyc_document', 'types'));
  }

  public function update(KycDocumentUpdateRequest $request, KycDocument $kyc_document)
  {
    $kyc_document->update($request->only(['title', 'status', 'required_from', 'fields']));

    return $this->sendRes(__('Kyc Document Updated Successfully'), ['event' => 'redirect', 'url' => route('admin.kyc-documents.index')]);
  }
}
