<?php

namespace App\Http\Controllers\Admin\ContractDoc;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\ContractDoc\DocControlsDataTable;
use App\Http\Requests\Admin\ContractDoc\DocControllStoreRequest;
use App\Http\Requests\Admin\KycDocumentUpdateRequest;
use App\Models\ContractCategory;
use App\Models\ContractType;
use App\Models\KycDocument;

class DocControlController extends Controller
{
  public function index(DocControlsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.contract-doc-controls.index');
  }

  public function create()
  {
    $data['types'] = KycDocument::TYPES;
    $data['kyc_document'] = new KycDocument();
    $data['contract_types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    $data['contract_categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Category'), '');

    return view('admin.pages.contract-doc-controls.create', $data);
  }

  public function store(DocControllStoreRequest $request)
  {
    $data['fields'] = $this->addIDs($request->fields);
    $data['is_expirable'] = $request->boolean('is_expirable');
    $data['workflow'] = 'Contract Required Docs';
    $data['required_from'] = 0;
    KycDocument::create($data + $request->validated());

    return $this->sendRes(__('Document Added Successfully'), ['event' => 'redirect', 'url' => route('admin.contract-doc-controls.index')]);
  }

  public function edit(KycDocument $contractDocControl)
  {
    $data['types'] = KycDocument::TYPES;
    $data['kyc_document'] = $contractDocControl;
    $data['contract_types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    $data['contract_categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Category'), '');

    return view('admin.pages.contract-doc-controls.create', $data);
  }

  public function update(DocControllStoreRequest $request, KycDocument $contractDocControl)
  {
    $data['fields'] = $this->addIDs($request->fields);
    $data['is_expirable'] = $request->boolean('is_expirable');
    $contractDocControl->update($data + $request->validated());

    return $this->sendRes(__('Document Updated Successfully'), ['event' => 'redirect', 'url' => route('admin.contract-doc-controls.index')]);
  }

  protected function addIDs($fields)
  {
    foreach ($fields as $key => $field) {
      $fields[$key]['id'] = @$fields[$key]['id'] ?? uniqid();
    }

    return $fields;
  }
}
