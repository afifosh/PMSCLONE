<tr class="expanded-edit-row" style="background-color: #efb7c461">
  <td colspan="3">
  {!! Form::open(['route' => ['admin.phases.subtotal-adjustments.store',  ['phase' => $phase->id,]], 'method' => 'POST']) !!}

  <div class="row">
    <div class="form-group col-12">
      {{ Form::label('adjuted_subtotal_amount', __('Adjusted Subtotal Amount'), ['class' => 'col-form-label']) }}
      {!! Form::number('adjuted_subtotal_amount', $phase->subtotal_row, ['class' => 'form-control tax-amount', 'placeholder' => __('Adjusted Subtotal Amount')])!!}
    </div>
  </div>

  <div class="mt-3 d-flex justify-content-end">
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
          <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
      </div>
  </div>
{!! Form::close() !!}