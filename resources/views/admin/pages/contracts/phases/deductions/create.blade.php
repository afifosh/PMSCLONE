<tr class="expanded-edit-row" style="background-color: #efb7c461">
  <td colspan="3">
  @if ($deduction->id)
      {!! Form::model($phase, ['route' => ['admin.contracts.phases.deductions.update', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'deduction' => $deduction->id]],
        'method' => 'PUT',
        'class' => 'phase-create-form',
        'id' => 'phase-update-form',
        'data-phase-id' => $phase->id,
      ]) !!}
  @else
      {!! Form::model($phase, ['route' => ['admin.contracts.phases.deductions.store',  ['contract' => $phase->contract_id, 'phase' => $phase->id,]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
  @endif

  @include('admin.pages.contracts.phases.deductions.deduction-field')

  <div class="mt-3 d-flex justify-content-end">
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
          <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
      </div>
  </div>
{!! Form::close() !!}
