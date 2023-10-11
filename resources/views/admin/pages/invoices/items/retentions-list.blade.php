{!! Form::open(['route' => ['admin.invoices.invoice-items.store', [$invoice->id, 'type' => 'retentions']], 'method' => 'POST']) !!}

<div class="row">
  <div class="table-responsive">
    <table class="table" id="retentions-invoice-item-table">
      <thead>
        <tr>
          <th>{!! Form::checkbox('checkAll', 1, 0, ['class' => 'form-check-input retention-check-all']) !!}</th>
          <th scope="col">Invoice</th>
          <th scope="col">Retention Amount</th>
        </tr>
      </thead>
      <tbody>
        {{-- @forelse ($invoices as $invoice)
        <tr class="">
          <th>{!! Form::checkbox('retentions[]', $invoice->id, 0, ['class' => 'form-check-input retention-item']) !!}</th>
          <td>{{runtimeInvIdFormat($invoice->id)}}</td>
          <td>@cMoney(-$invoice->retention_amount, $invoice->contract->currency, true)</td>
        </tr>
        @empty
        @endforelse --}}
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Add Selected') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
<script>
  $(document).ready(function() {
    $('.phase-check-all').on('change', function() {
      if ($(this).is(':checked')) {
        $('.phase-item').prop('checked', true);
      } else {
        $('.phase-item').prop('checked', false);
      }
    });
    $('.phase-item').on('change', function() {
      if ($('.phase-item:checked').length == $('.phase-item').length) {
        $('.phase-check-all').prop('checked', true);
      } else {
        $('.phase-check-all').prop('checked', false);
      }
    });
  });
</script>
