<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Http\Request;

class RetentionController extends Controller
{
  public function destroy(Invoice $invoice)
  {
    if($invoice->retention_released_at)
      return $this->sendErr('Retention already released');

    $invoice->update([
      'retention_id' => null,
      'retention_name' => null,
      'retention_percentage' => 0,
      'retention_amount' => 0,
      'retention_manual_amount' => 0,
      'retention_released_at' => null,
    ]);

    return $this->sendRes('Retention removed successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
