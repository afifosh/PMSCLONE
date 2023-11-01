@if ($signature->id)
  {!! Form::model($signature, ['route' => ['admin.doc-signatures.update', ['doc_signature' => $signature]],
      'method' => 'PUT',
  ]) !!}
@else
  {!! Form::model($signature, ['route' => ['admin.doc-signatures.store',  ['doc' => $doc, 'signature' => $is_signature]], 'method' => 'POST']) !!}
@endif
@php
    $is_signature = $is_signature || $signature->is_signature;
    $model = $is_signature ? 'Sign' : 'Stamp';
@endphp
<div class="row">
  <div class="form-group col-6">
      {{ Form::label('signer_id', __($model.'er'), ['class' => 'col-form-label']) }}
      {!! Form::select('signer_id', $signers ?? [], null, [
        'class' => 'form-control globalOfSelect2UserRemote',
        'data-url' => route('resource-select-user', ['Admin']),
        'data-placeholder' => __('Select') . ' '. __($model.'er'),
        'data-allow-clear' => 'true'
      ]) !!}
  </div>
  {{-- signer position --}}
  <div class="form-group col-6">
      {{ Form::label('signer_position', __('Position'), ['class' => 'col-form-label']) }}
      {!! Form::text('signer_position', null, ['class' => 'form-control', 'placeholder' => __('Position')]) !!}
  </div>
  {{-- signed_at --}}
  <div class="form-group col-12">
      {{ Form::label('signed_at', __($model.'ed at'), ['class' => 'col-form-label']) }}
      {!! Form::date('signed_at', $signature->signed_at, ['class' => 'form-control flatpickr', 'placeholder' => __($model.'ed at')]) !!}
  </div>
</div>
<div class="mt-3 d-flex justify-content-end">
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>
{!! Form::close() !!}
