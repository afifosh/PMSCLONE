<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use Illuminate\Foundation\Http\FormRequest;

class PhaseStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation(): void
  {
    $this->merge([
      'is_allowable_cost' => $this->boolean('is_allowable_cost'),
      'is_reviewed' => $this->boolean('is_reviewed'),
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'stage_id' => 'required',
      'name' => 'required|string|max:255|unique:contract_phases,name,NULL,id,stage_id,' . $this->stage_id,
      'estimated_cost' => [
        'required',
        'numeric',
        'gte:0',
        //'max:' .  ($this->contract->remaining_amount - $this->calculated_tax_amount))
      ],
      'is_allowable_cost' => 'required|boolean',
      'is_reviewed' => 'required|boolean',
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->contract->end_date,
    ];
  }

  public function messages()
  {
    return [
      'estimated_cost.max' => '',
      'stage_id.exists' => 'Please select a valid stage for this contract.',
      'stage_id.required' => 'The stage field is required. Please select a stage.',
    ];
  }
}
