<?php

namespace App\Http\Requests\Admin\Applications\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class PipelineStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'stages' => 'required|array',
      // stage name shoulde be unique in array
      'stages.*.name' => [
        'required',
        'string',
        'max:255',
        function ($attribute, $value, $fail) {
          $stages = $this->input('stages');
          $stageNames = array_column($stages, 'name');
          $duplicates = array_unique(array_diff_assoc($stageNames, array_unique($stageNames)));
          if (count($duplicates) > 0) {
            $fail('Stage name should be unique');
          }
        },
      ],
    ];
  }
}
