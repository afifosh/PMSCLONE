{!! Form::model($file, ['route' => ['admin.draft-rfps.files.update', [ 'draft_rfp' => $file->rfp_id, 'file' => $file]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}

<div class="row">
    <div class="form-group">
      {{ Form::label('title', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('title', pathinfo($file->title)['filename'], ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
