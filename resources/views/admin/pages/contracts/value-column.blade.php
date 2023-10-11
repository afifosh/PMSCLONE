<div class="text-start">Value: @cMoney($contract->value ?? 0, $contract->currency, true)
  <p class="my-0"><span class="text-warning mt-1">Invoiced:</span> @cMoney($contract->total ?? 0, $contract->currency, true)</p>
  <div><span class="text-muted">Tax:</span> @cMoney($contract->total_tax ?? 0, $contract->currency, true)</div>
  <div><span class="text-success">Paid:</span> @cMoney($contract->paid_amount ?? 0, $contract->currency, true)</div>
  <div><span class="text-danger">UnPaid:</span> @cMoney($contract->due_amount ?? 0, $contract->currency, true)</div>
</div>
