{!! Form::open(['route' => ['admin.invoices.invoice-items.store', $invoice->id], 'method' => 'POST']) !!}

<div class="row">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>{!! Form::checkbox('checkAll', 1, 0, ['class' => 'form-check-input milestone-check-all']) !!}</th>
          <th scope="col">Milestone</th>
          <th scope="col">Cost</th>
          <th scope="col">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($milestones as $milestone)
        <tr class="">
          <th>{!! Form::checkbox('milestones[]', $milestone->id, 0, ['class' => 'form-check-input milestone-item']) !!}</th>
          <td>{{$milestone->name}}</td>
          <td>{{$milestone->estimated_cost}}</td>
          <td>{{$milestone->status}}</td>
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
    $('.milestone-check-all').on('change', function() {
      if ($(this).is(':checked')) {
        $('.milestone-item').prop('checked', true);
      } else {
        $('.milestone-item').prop('checked', false);
      }
    });
    $('.milestone-item').on('change', function() {
      if ($('.milestone-item:checked').length == $('.milestone-item').length) {
        $('.milestone-check-all').prop('checked', true);
      } else {
        $('.milestone-check-all').prop('checked', false);
      }
    });
  });
</script>
