<form action="{{route('admin.invoices.merge-invoices.store', [$invoice])}}" method="post">
  <div class="row">
    <div class="table-responsive">
      <table class="table" id="mergeable-invoices-table">
        <thead>
          <tr>
            <th>{!! Form::checkbox('checkAll', 1, 0, ['class' => 'form-check-input phase-check-all']) !!}</th>
            <th scope="col">Invoice</th>
            <th scope="col">Total Amount</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>{!! Form::checkbox('invoice_ids[]', $inv->id, 0, ['class' => 'form-check-input phase-item']) !!}</td>
              <td>{{ runtimeInvIdFormat($inv->id) }}</td>
              <td>@money($inv->total, $invoice->contract->currency, true)</td>
              <td>{{ $inv->status }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">No mergeable invoices found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">
      <div class="btn-flt float-start">
        <div class="form-group">
          {{-- Cancell After merging --}}
          {!! Form::checkbox('cancel_merged', 1, 0, ['class' => 'form-check-input', 'id' => 'cancel_merged']) !!}
          {!! Form::label('cancel_merged', __('Cancel Merged Invoices Instead Of Deleting'), ['class' => 'form-label']) !!}
        </div>
      </div>
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Merge Selected') }}</button>
      </div>
  </div>
</form>
