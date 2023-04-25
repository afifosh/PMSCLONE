<?php

namespace App\Http\Requests\Company\CompanyProfile;

use App\Http\Controllers\Company\CompanyProfile\DocumentController;
use App\Models\KycDocument;
use Illuminate\Foundation\Http\FormRequest;

class KycDocumentUpdateRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    $locality = auth()->user()->company->getPOCLocalityType();
    $rules = [];
    $document = KycDocument::whereIn('required_from', [3, $locality])->where('status', 1)->findOrFail(request()->document_id);
    $avail_rules = KycDocument::VALIDATIONS;

    $att = array_filter(request()->fields, function($value) {
        return $value != null;
    });

    if (!request()->expiry_date && empty($att) && !$document->is_mendatory) {
      request()->escapedRules = true;
      return [];
    }

    if (!$document) {
      return $rules;
    }
    foreach ($document->fields as $i => $field) {
      $isRequired = ['nullable'];
      if ($field['is_required'])
        $isRequired = ['required'];
      $rules['fields.' . $field['id']] = array_merge($isRequired, $avail_rules[$field['type']]);
    }
    if($document->is_expirable && $document->is_expiry_date_required){
      $rules['expiry_date'] = ['required', 'date'];
    }

    return $rules;
  }

  public function messages()
  {
    $messages = [];
    $locality = auth()->user()->company->getPOCLocalityType();
    $document = KycDocument::whereIn('required_from', [3, $locality])->where('status', 1)->findOrFail(request()->document_id);
    if (!$document) {
      return $messages;
    }
    $messages['*.required'] = 'This field is required';
    $messages['*.*.required'] = 'This field is required';

    return $messages;
  }
}
