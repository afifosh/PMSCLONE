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
}
