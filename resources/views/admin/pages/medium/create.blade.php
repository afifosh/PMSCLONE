@if ($medium->id)
  {!! Form::model($medium, ['route' => ['admin.mediums.update', $medium->id], 'method' => 'PUT', 'id' => 'stage-create-form']) !!}
@else
  {!! Form::model($medium, ['route' => ['admin.mediums.store'], 'method' => 'POST', 'id' => 'stage-create-form']) !!}
@endif  
<div class="row">
  {{-- name --}}
  <div class="form-group col-12">
    {{ Form::label('name', __('Medium Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
</div>
<div class="mt-3 d-flex justify-content-end">
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>

{!! Form::close() !!}
