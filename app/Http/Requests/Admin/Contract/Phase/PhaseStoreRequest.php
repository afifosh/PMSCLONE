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
    $stage = ContractStage::find($this->route('stage')) ?? collect([]);

    return [
      // if phase is from contract stage then check if name is unique in contract stages and contract_id,
      //else check if name is unique in contract phases
      'name' => 'required|string|max:255|'. (($stage instanceof ContractStage) ? 'unique:contract_phases,name,NULL,id,contract_id,' . $this->contract->id : 'unique:contract_stages,name,NULL,id,contract_id,' . $this->contract->id),
      // if phase is from contract stage then check if estimated cost is less than stage remaining amount,
      //else check if estimated cost is less than contract remaining amount
      'estimated_cost' => ['required', 'numeric', 'min:0' , 'max:' . (($stage instanceof ContractStage) ? $stage->remaining_amount : $this->contract->remaining_amount)],
      'description' => 'nullable|string|max:2000',
      // if phase is from contract stage then check if start date is after or equal to stage start date,
      //else check if start date is after or equal to contract start date
      'start_date' => 'required|date'. (request()->due_date ? '|before_or_equal:due_date' : '' ).'|after_or_equal:' . (($stage instanceof ContractStage) ? $stage->start_date : $this->contract->start_date),
      // if phase is from contract stage then check if due date is after stage start date and before or equal to stage due date,
      //else check if due date is after contract start date and before or equal to contract due date
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . (($stage instanceof ContractStage) ? $stage->due_date : $this->contract->end_date),
    ];

  }
}
