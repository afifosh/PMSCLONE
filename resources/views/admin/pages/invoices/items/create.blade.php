{!! Form::open(['route' => ['admin.invoices.invoice-items.store', $invoice->id], 'method' => 'POST']) !!}

<div class="row">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>{!! Form::checkbox('checkAll', 1, 0, ['class' => 'form-check-input phase-check-all']) !!}</th>
          <th scope="col">Phase</th>
          <th scope="col">Cost</th>
          <th scope="col">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($phases as $phase)
        <tr class="">
          <th>{!! Form::checkbox('phases[]', $phase->id, 0, ['class' => 'form-check-input phase-item']) !!}</th>
          <td>{{$phase->name}}</td>
          <td>@money($phase->estimated_cost, $phase->contract->currency, true)</td>
          <td>{{$phase->status}}</td>
        </tr>
        @empty
        @endforelse
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