@if ($private_note->id)
    {!! Form::model($private_note, ['route' => ['admin.private-notes.update', [$private_note]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($private_note, ['route' => ['admin.private-notes.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
      {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Title')]) !!}
  </div>

  {{-- status --}}
  <div class="form-group col-6">
    {{ Form::label('tag', __('Tag'), ['class' => 'col-form-label']) }}
    {!! Form::select('tag', $tags, $private_note->tag, ['class' => 'form-select globalOfSelect2']) !!}
  </div>

</div>
<div class="col-md-12 mt-3">
  <div class="mb-3">
    {!! Form::label('description', 'Description', ['class' => 'col-form-label']) !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) !!}
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
