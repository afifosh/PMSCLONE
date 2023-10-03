<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\ContractStage;
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
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255|unique:contract_phases,name,NULL,id,stage_id,' . $this->stage->id,
      'estimated_cost' => ['required', 'numeric', 'gt:0' , 'max:' . $this->stage->remaining_amount],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date'. (request()->due_date ? '|before_or_equal:due_date' : '' ).'|after_or_equal:' . $this->stage->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->stage->due_date,
    ];

  }
}
