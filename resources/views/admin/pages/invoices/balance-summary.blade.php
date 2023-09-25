<tr>
  <td class="x-payments-lang">Payments : </td>
  <td class="x-payments"> <a data-toggle="ajax-modal" data-title="Invoice Payments" data-href="{{route('admin.invoices.payments.index', [$invoice, 'accepts' => 'view_data'])}}" class="cursor-pointer"><span class="p-l-20">@money($invoice->paid_amount, $invoice->contract->currency, true)</span></a></td>
</tr>
<tr>
  <td class="x-balance-due-lang">Balance Due : </td>
  <td class="x-balance-due">
    <span class="x-due-amount-label">
        <label class="label label-rounded label-danger text-white badge bg-label-primary">@money($invoice->total - $invoice->paid_amount, $invoice->contract->currency, true)</label>
    </span>
  </td>
</tr>
