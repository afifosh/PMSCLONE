<?php

namespace App\Http\Controllers\Admin\Contract\UploadedDoc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocSignature\DocSignatureStoreRequest;
use App\Models\DocSignature;
use App\Models\UploadedKycDoc;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
  public function create(Request $request)
  {
    $uploadedDoc = UploadedKycDoc::with('requestedDoc')->withCount(['signatures', 'stamps'])->findOrFail($request->doc);

    if($request->signature && $uploadedDoc->signatures_count >= $uploadedDoc->requestedDoc->signatures_required){
      return $this->sendError('Maximum allowed signatures are added');
    }elseif(!$request->signature && $uploadedDoc->stamps_count >= $uploadedDoc->requestedDoc->stamps_required){
      return $this->sendError('Maximum allowed stamps are added');
    }

    $data['doc'] = $request->doc;
    $data['is_signature'] = $request->signature;
    $data['signature'] = new DocSignature();

    return $this->sendRes('success', ['view_data' => view('admin.pages.docs-signatures.create', $data)->render()]);
  }

  public function store(DocSignatureStoreRequest $request)
  {
    $doc = UploadedKycDoc::with('requestedDoc')->withCount(['signatures', 'stamps'])->findOrFail($request->doc);

    if($request->signature && $doc->signatures_count >= $doc->requestedDoc->signatures_required){
      return $this->sendError('Maximum allowed signatures are added');
    }elseif(!$request->signature && $doc->stamps_count >= $doc->requestedDoc->stamps_required){
      return $this->sendError('Maximum allowed stamps are added');
    }

    if ($request->is_signature)
      $doc->signatures()->create($request->validated());
    else
      $doc->stamps()->create($request->validated());

    return $this->sendRes(__('Created Successfully'), ['event' => 'table_reload', 'table_id' => $request->is_signature ? 'signatures-table' : 'stamps-table', 'close' => 'globalModal']);
  }

  public function edit(DocSignature $docSignature)
  {
    $data['signature'] = $docSignature;
    $data['is_signature'] = $docSignature->is_signature;
    $data['doc'] = $docSignature->uploaded_kyc_doc_id;
    $modalTitle = __('Edit ' . ($docSignature->is_signature ? 'Signature' : 'Stamp'));

    return $this->sendRes('success', ['view_data' => view('admin.pages.docs-signatures.create', $data)->render(), 'modalTitle' => $modalTitle]);
  }
  public function update(DocSignatureStoreRequest $request, DocSignature $docSignature)
  {
    $docSignature->update($request->validated());

    return $this->sendRes(__('Updated Successfully'), ['event' => 'table_reload', 'table_id' => $request->is_signature ? 'signatures-table' : 'stamps-table', 'close' => 'globalModal']);
  }

  public function destroy(DocSignature $docSignature)
  {
    $table = $docSignature->is_signature ? 'signatures' : 'stamps';
    $docSignature->delete();

    return $this->sendRes(__('Deleted Successfully'), ['event' => 'table_reload', 'table_id' => $table . '-table']);
  }
}
