@if ($phase->id)
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.update', ['project' => 'project', 'contract' => $contract, 'phase' => $phase->id, 'stage' => $stage, 'tableId' => request()->tableId]],
      'method' => 'PUT',
      'class' => 'phase-create-form',
      'id' => 'phase-update-form',
      'data-phase-id' => $phase->id,
    ]) !!}
@else
    {!! Form::model($phase, ['route' => ['admin.projects.contracts.stages.phases.store',  ['project' => 'project', 'contract' => $contract, 'stage' => $stage, 'tableId' => request()->tableId]], 'method' => 'POST', 'class' => 'phase-create-form',]) !!}
@endif

<div id="phase-addons">
  @include('admin.pages.contracts.phases.show.show')
</div>

@if($phase->id)
  <div class="mt-3 d-flex justify-content-between">
    <div class="phase-editing-users">
    </div>
      <div class="btn-flt float-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      </div>
  </div>
@endif
{!! Form::close() !!}
