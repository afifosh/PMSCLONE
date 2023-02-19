@if ($draft_rfp->id)
    {!! Form::model($draft_rfp, ['route' => ['admin.draft-rfps.update', $draft_rfp->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($draft_rfp, ['route' => ['admin.draft-rfps.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    <div class="form-group col-6">
      {{ Form::label('name', __('RFP Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    <div class="form-group col-6">
      {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
      {!! Form::select('program_id', $programs, $draft_rfp->program_id, ['class' => 'form-select globalOfSelect2']) !!}
    </div>

    <div class="form-group">
      {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'required'=> 'true', 'rows' => 5, 'placeholder' => __('Description')]) !!}
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
