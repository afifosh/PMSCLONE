<tr class="expanded-edit-row" style="background-color: var(--bs-light)">
  <td colspan="3">
    @if ($tax->id)
    {!! Form::model($phase, ['route' => ['admin.contracts.phases.taxes.update', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'tax' => $tax->id]],
      'method' => 'PUT',
      'class' => 'phase-create-form',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
    @else
        {!! Form::model($phase, ['route' => ['admin.contracts.phases.taxes.store',  ['contract' => $phase->contract_id, 'phase' => $phase->id,]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
    @endif

    @include('admin.pages.contracts.phases.taxes.tax-field')

    <div class="mt-3 d-flex justify-content-end">
        <div class="btn-flt float-end">
            <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
            <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div>

    {!! Form::close() !!}
  </td>
</tr>
