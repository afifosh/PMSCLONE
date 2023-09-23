<tr>
  <td class="x-payments-lang">Payments : </td>
  <td class="x-payments"> <span class="text-primary"><span class="p-l-20">{{$invoice->paid_amount}}</span></span></td>
</tr>
<tr>
  <td class="x-balance-due-lang">Balance Due : </td>
  <td class="x-balance-due">
    <span class="x-due-amount-label">
        <label class="label label-rounded label-danger text-primary">{{$invoice->total - $invoice->paid_amount}}</label>
    </span>
  </td>
</tr>
