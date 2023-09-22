<div class="text-start">Total: {{$invoice->total}}
    <p class="my-0"><span class="text-success mt-1">Paid:</span> {{$invoice->paid_amount}}</p>
    <span class="text-danger">Unpaid:</span> {{$invoice->total - $invoice->paid_amount}}
</div>
