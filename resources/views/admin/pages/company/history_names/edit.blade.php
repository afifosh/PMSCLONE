{!! Form::open(['route' => ['admin.companies.names.update', ['company' => $name->model_id, $name->id]], 'method' => 'PUT',]) !!}

<div class="row">
    <div class="form-group col-12">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', $name->name, [
          'class' => 'form-control',
          'placeholder' => __('Name')
        ]) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Update') }}</button>
    </div>
</div>
{!! Form::close() !!}
