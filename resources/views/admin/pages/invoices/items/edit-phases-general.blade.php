{{-- Stage --}}
<div class="form-group col-6">
  {{ Form::label('stage_id', __('Stage'), ['class' => 'col-form-label']) }}
  {!! Form::select('stage_id', $stages ?? [], $invoiceItem->invoiceable->stage_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Stage')]) !!}
</div>
{{-- Phase --}}
<div class="form-group col-6">
  {{ Form::label('phase_id', __('Phase'), ['class' => 'col-form-label']) }}
  {!! Form::select('phase_id', $phases ?? [], $invoiceItem->invoiceable_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Phase')]) !!}
</div>
{{-- Subtotal --}}
{{-- <div class="form-group col-6">
  {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
  {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
</div> --}}
