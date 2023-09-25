<div class="text-start">Total: @money($invoice->total, $invoice->contract->currency, true)
    <p class="my-0"><span class="text-success mt-1">Paid:</span> @money($invoice->paid_amount, $invoice->contract->currency, true)</p>
    <span class="text-danger">Unpaid:</span> @money($invoice->total - $invoice->paid_amount, $invoice->contract->currency, true)
</div>
