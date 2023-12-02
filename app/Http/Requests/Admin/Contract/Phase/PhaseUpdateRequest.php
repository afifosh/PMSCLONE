<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use Illuminate\Foundation\Http\FormRequest;

class PhaseUpdateRequest extends FormRequest
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
      'stage_id' => 'required',
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $this->phase->id . ',id,stage_id,' . $this->stage_id,
      'estimated_cost' => [
        'required',
        'numeric',
        'gt:0',
        //'max:' .  ($this->contract->remaining_amount - $this->calculated_tax_amount))
      ],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->contract->end_date,
    ];
  }

  public function messages()
  {
    return [
      'estimated_cost.max' => '',
      'stage_id.required' => 'The stage field is required. Please select a stage.',
    ];
  }
}
