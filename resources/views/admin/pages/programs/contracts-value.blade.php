<div class="text-start">
  <div class="my-0 d-flex"><span class="text-muted">Value: </span> @cMoney($program->contracts_value ?? 0, $program->contracts[0]->currency ?? 'SAR', true)</div>
  <div class="my-0 d-flex"><span class="text-warning">Invoiced: </span> @cMoney($program->invoices_total ?? 0, $program->contracts[0]->currency ?? 'SAR', true)</div>
  <div class="my-0 d-flex"><span class="text-success">Paid: </span> @cMoney($program->invoices_paid_amount ?? 0, $program->contracts[0]->currency ?? 'SAR', true)</div>
</div>
