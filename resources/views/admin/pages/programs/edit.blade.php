@if ($program->id)
    {!! Form::model($program, ['route' => ['admin.programs.update', $program->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($program, ['route' => ['admin.programs.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    <div class="form-group">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    <div class="form-group">
        {{ Form::label('program_code', __('Program Code'), ['class' => 'col-form-label']) }}
        {!! Form::text('program_code', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Program Code')]) !!}
    </div>

    <div class="form-group">
      {{ Form::label('parent_id', __('Parent Program'), ['class' => 'col-form-label']) }}
      {!! Form::select('parent_id', $programs, $program->parent_id, ['class' => 'form-select globalOfSelect2']) !!}
    </div>

    <div class="form-group">
      {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'required'=> 'true', 'rows' => 5, 'placeholder' => __('Description')]) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
