<?php

namespace App\Http\Requests\Company\CompanyProfile;

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
    $documents = KycDocument::whereIn('required_from', [3, $locality])->where('status', 1)->get();
    $avail_rules = KycDocument::VALIDATIONS;
    if ($documents->count() == 0) {
      return $rules;
    }
    foreach ($documents as $document) {
      foreach ($document->fields as $i => $field) {
        $isRequired = ['nullable'];
        if ($field['is_required'])
          $isRequired = ['required'];
        $rules['doc_' . $document->id . '_field_' . $i . '_' . $field['type']] = array_merge($isRequired, $avail_rules[$field['type']]);
      }
    }

    return $rules;
  }

  public function messages()
  {
    $messages = [];
    $locality = auth()->user()->company->getPOCLocalityType();
    $documents = KycDocument::whereIn('required_from', [3, $locality])->where('status', 1)->get();
    if ($documents->count() == 0) {
      return $messages;
    }
    foreach ($documents as $document) {
      foreach ($document->fields as $i => $field) {
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.required'] = 'The ' . $field['label'] . ' field is required.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.string'] = 'The ' . $field['label'] . ' must be a string.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.numeric'] = 'The ' . $field['label'] . ' must be a number.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.email'] = 'The ' . $field['label'] . ' must be a valid email address.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.max'] = 'The ' . $field['label'] . ' may not be greater than 255 characters.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.file'] = 'The ' . $field['label'] . ' must be a file.';
        $messages['doc_' . $document->id . '_field_' . $i . '_' . $field['type'] . '.mimetypes'] = 'The ' . $field['label'] . ' must be a image file.';
      }
    }

    return $messages;
  }
}
