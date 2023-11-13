{!! Form::open(['route' => ['admin.invoices.status.store', $invoice], 'method' => 'POST']) !!}
<div class="row">
  {{-- Status --}}
  <div class="form-group col-12">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    {{ Form::select('status', ['Void' => 'Void'], 'Void', ['class' => 'form-control', 'disabled', 'placeholder' => __('Select Status')]) }}
  </div>
  {{-- void_reason --}}
  <div class="form-group col-12">
    {{ Form::label('void_reason', __('Reason:'), ['class' => 'col-form-label']) }}
    {{ Form::textarea('void_reason', null, ['class' => 'form-control', 'placeholder' => __('Reason'), 'rows' => 5]) }}
  </div>
  <div class="mt-3 d-flex justify-content-end">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
  </div>
{!! Form::close() !!}
