<div class="d-flex justify-content-start align-items-center user-name">
    <div class="avatar-wrapper">
      <div class="avatar me-2"><i class="ti ti-license mb-2 ti-xl"></i></div>
    </div>
    <div class="d-flex flex-column">
      <span class="fw-medium mb-1">{{ $contract->subject }}</span>
      <small class="text-muted mb-1">{{ $contract->program->name }}</small>
      <span class="badge bg-label-{{$contract->getStatusColor()}} me-auto">{{$contract->status}}</span>
    </div>
  </div>
@if ($stage->id)
    {!! Form::model($stage, ['route' => ['admin.contracts.stages.update', ['contract' => $contract, 'stage' => $stage]],
        'method' => 'PUT',
        'id' => 'stage-update-form',
        'data-stage-id' => $stage->id,
    ]) !!}
@else
    {!! Form::model($stage, ['route' => ['admin.contracts.stages.store',  ['contract' => $contract]], 'method' => 'POST']) !!}
@endif
<div class="row">
  <div class="form-group col-12">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
</div>
<div class="mt-3 d-flex justify-content-between">
    <div class="stage-editing-users">
    </div>
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
