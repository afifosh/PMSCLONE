{!! Form::model($financialYear, ['route' => ['admin.finances.financial-years.transactions.store', [$financialYear]], 'method' => 'POST']) !!}

<div class="row">
    {{-- Amount --}}
    <div class="form-group col-6">
      {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label']) }}
      {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __('Amount')]) !!}
    </div>
    {{-- Type --}}
    <div class="form-group col-6">
      {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
      {!! Form::select('type', ['1' => 'Credit', '2' => 'Debit', '3' => 'Transfer'], null, ['class' => 'form-control globalOfSelect2']) !!}
    </div>

    {{-- program --}}
    <div class="form-group col-6 d-none transfer-program">
      {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
      {!! Form::select('program_id', [], null, ['class' => 'form-control globalOfSelect2Remote',
      'data-url' => route('resource-select', ['resource' => 'Program']),
      'placeholer' => __('Select Program')]) !!}
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
<script>
  $(document).on('change', 'select[name="type"]', function() {
    if ($(this).val() == 3) {
      $('.transfer-program').removeClass('d-none');
    } else {
      $('.transfer-program').addClass('d-none');
    }
  });
</script>
