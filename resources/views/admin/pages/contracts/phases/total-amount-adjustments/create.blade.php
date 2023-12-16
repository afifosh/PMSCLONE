<tr class="expanded-edit-row" style="background-color: var(--bs-light)">
  <td colspan="3">
  {!! Form::open(['route' => ['admin.contracts.phases.total-amount-adjustments.store',  ['contract' => $phase->contract_id, 'phase' => $phase->id,]], 'method' => 'POST']) !!}

  <div class="row">
    <div class="form-group col-12">
      {{ Form::label('adjuted_total_amount', __('Adjusted Total Amount'), ['class' => 'col-form-label']) }}
      {!! Form::number('adjuted_total_amount', $phase->total_cost, ['class' => 'form-control tax-amount', 'placeholder' => __('Adjusted Total Amount')])!!}
    </div>
  </div>

  <div class="mt-3 d-flex justify-content-end">
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
          <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
      </div>
  </div>
{!! Form::close() !!}
