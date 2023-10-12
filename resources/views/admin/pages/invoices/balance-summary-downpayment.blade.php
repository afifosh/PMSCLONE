<tr>
  <td class="x-payments-lang">Payments : </td>
  <td class="x-payments"> <a data-toggle="ajax-modal" data-title="Invoice Payments" data-href="{{route('admin.invoices.payments.index', [$invoice, 'accepts' => 'view_data'])}}" class="cursor-pointer"><span class="p-l-20">@cMoney($invoice->paid_amount, $invoice->contract->currency, true)</span></a></td>
</tr>
<tr>
  <td class="x-balance-due-lang">Balance Due : </td>
  <td class="x-balance-due">
    <span class="x-due-amount-label">
        <label class="label label-rounded label-danger text-white badge bg-label-primary">@cMoney($invoice->payableAmount(), $invoice->contract->currency, true)</label>
    </span>
  </td>
</tr>

<tr>
  <td class="x-balance-due-lang">Deducted Amount : </td>
  <td class="x-balance-due">
    <span class="x-due-amount-label">
        <label class="label label-rounded label-danger badge bg-label-warning">@cMoney($invoice->total - $invoice->downpaymentAmountRemaining(), $invoice->contract->currency, true)</label>
    </span>
  </td>
</tr>
<tr>
  <td class="x-balance-due-lang">Deductable Remaining Amount : </td>
  <td class="x-balance-due">
    <span class="x-due-amount-label">
        <label class="label label-rounded label-danger badge bg-label-success">@cMoney($invoice->downpaymentAmountRemaining(), $invoice->contract->currency, true)</label>
    </span>
  </td>
</tr>
