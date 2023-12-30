<tr class="expanded-edit-row" style="background-color: var(--bs-light)">
  <td colspan="3">
  @if ($costAdjustment->id)
      {!! Form::model($costAdjustment, ['route' => ['admin.contracts.phases.cost-adjustments.update', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'cost_adjustment' => $costAdjustment->id]],
        'method' => 'PUT',
        'class' => 'phase-create-form',
        'id' => 'phase-update-form',
        'data-phase-id' => $phase->id,
      ]) !!}
  @else
      {!! Form::model($costAdjustment, ['route' => ['admin.contracts.phases.cost-adjustments.store',  ['contract' => $phase->contract_id, 'phase' => $phase->id,]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
  @endif

  <div class="row">
    {{-- Amount input --}}
    <div class="col-12">
      <div class="form-group">
        {!! Form::label('amount', __('Amount'), ['class' => 'form-label']) !!}
        {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __('Amount')]) !!}
      </div>
    </div>

    {{-- Description input --}}
    <div class="col-12">
      <div class="form-group">
        {!! Form::label('description', __('Description'), ['class' => 'form-label']) !!}
        {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => __('Description')]) !!}
      </div>
    </div>
  </div>

  <div class="mt-3 d-flex justify-content-end">
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
          <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
      </div>
  </div>
{!! Form::close() !!}
