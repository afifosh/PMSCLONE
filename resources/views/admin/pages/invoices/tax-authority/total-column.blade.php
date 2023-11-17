<div class="text-start">Total: @cMoney($invoice->total, $invoice->invoice->contract->currency, true)
  <p class="my-0"><span class="text-success mt-1">Paid:</span> @cMoney($invoice->paid_amount, $invoice->invoice->contract->currency, true)</p>
  <div><span class="text-danger">Unpaid:</span> @cMoney($invoice->total - $invoice->paid_amount, $invoice->invoice->contract->currency, true)</div>
</div>
