<div class="text-start">@cMoney($contract->value ?? 0, $contract->currency, true)
  <p class="my-0"><span class="text-warning mt-1">Invoiced:</span> @cMoney($contract->total ?? 0, $contract->currency, true)</p>
  <div><span class="text-muted">Tax:</span> @cMoney($contract->total_tax ?? 0, $contract->currency, true)</div>
  <div><span class="text-success">Paid:</span> @cMoney($contract->paid_amount ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">UnPaid:</span> @cMoney($contract->due_amount ?? 0, $contract->currency, true)</div>
  <div><span class="text-warning">Retention:</span> @cMoney($contract->pending_retentions_sum / 1000 ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">Total WHT:</span> @cMoney($contract->total_wht ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">Paid WHT:</span> @cMoney($contract->paid_wht_amount ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">Total RC:</span> @cMoney($contract->total_rc ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">Paid RC:</span> @cMoney($contract->paid_rc_amount ?? 0, $contract->currency, true)</div>
</div>
