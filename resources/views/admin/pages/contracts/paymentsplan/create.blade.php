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
