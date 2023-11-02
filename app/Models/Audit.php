<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Models\Audit as ModelsAudit;

class Audit extends ModelsAudit
{
  use HasFactory;

  public $casts = [
    'old_values' => 'json',
    'new_values' => 'json',
    'created_at' => 'datetime:d M,Y H:i:s',
    'updated_at' => 'datetime:d M,Y H:i:s',
  ];

  /**
   * filter by contract, it will return all logs of contract and its stages and phases
   */
  public function scopeOfContract($q, $contract_id)
  {
    return
      $q->whereHasMorph('auditable', [Contract::class], function ($query) use ($contract_id) {
        $query->where('id', $contract_id);
      })
      ->orWhereHasMorph('auditable', [ContractStage::class], function ($query) use ($contract_id) {
        $query->whereHas('contract', function ($query) use ($contract_id) {
          $query->where('contract_id', $contract_id);
        });
      })
      ->orWhereHasMorph('auditable', [ContractPhase::class], function ($query) use ($contract_id) {
        $query->whereHas('stage', function ($query) use ($contract_id) {
          $query->whereHas('contract', function ($query) use ($contract_id) {
            $query->where('contract_id', $contract_id);
          });
        });
      })
      ->orwhere(function ($query) use ($contract_id) {
        $query->whereJsonContains('old_values->contract_id', $contract_id)
          ->where('auditable_type', ContractStage::class);
      });
  }


  public function renderCreatedMessage(): string
  {
      return 'created ' . $this->getModelClassName();
  }

  public function renderDeletedMessage(): string
  {
      return 'deleted ' . $this->getModelClassName();
  }

  private function beautifyFieldName(string $field): string
  {
      $words = explode('_', $field);
      $capitalizedWords = array_map('ucfirst', $words);
  
      return implode(' ', $capitalizedWords);
  }
  
  public function renderFieldAudit(): string
  {
      $auditData = $this->old_values;
      
      $messages = [];
      foreach ($auditData as $field => $oldValue) {
          $newValue = data_get($this->new_values, $field);
  
          // Beautify the field name
          $beautifiedField = ucwords(str_replace('_', ' ', $field));

          // Checking for 'cost' or 'amount' in the field name (case-insensitive) and if the value is numeric and non-zero
          if ((stripos($beautifiedField, 'Cost') !== false || stripos($beautifiedField, 'Amount') !== false)) {
              if (is_numeric($newValue) && $newValue != 0) {
                  $newValue /= 1000;
              }
              if (is_numeric($oldValue) && $oldValue != 0) {
                  $oldValue /= 1000;
              }
          }          
          
          // Check if the value was changed or set for the first time
          if ($oldValue === null) {
              $messages[] = 'Set <span class="mb-1 badge bg-label-secondary text-wrap text-start d-inline-block">' . $beautifiedField . '</span> to <span class="mb-1 badge bg-label-success text-wrap text-start d-inline-block">' . $newValue . '</span>';
          } else if ($oldValue != $newValue) {
              $messages[] = 'Changed <span class="mb-1 badge bg-label-secondary text-wrap text-start d-inline-block">' . $beautifiedField . '</span> from <span class="mb-1 badge bg-label-danger text-decoration-line-through text-wrap text-start d-inline-block">' . $oldValue . '</span> to <span class="mb-1 badge bg-label-success text-wrap text-start d-inline-block">' . $newValue . '</span>';
          }
      }
  
      return implode('<br>', $messages);
  }
  
  
  private function getModelClassName(): string
  {
      return class_basename($this->auditable_type);
  }

  // You can add other necessary functions...

}
