<div class="text-start">Total: @cMoney($invoice->total, $invoice->contract->currency, true)
    <p class="my-0"><span class="text-success mt-1">Paid:</span> @cMoney($invoice->paid_amount, $invoice->contract->currency, true)</p>
    <div><span class="text-danger">Unpaid:</span> @cMoney($invoice->total - $invoice->paid_amount, $invoice->contract->currency, true)</div>
    <span class="text-warning">Retention:</span> @cMoney($invoice->retention_amount, $invoice->contract->currency, true)
</div>
