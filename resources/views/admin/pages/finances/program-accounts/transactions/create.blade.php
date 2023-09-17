{!! Form::model($programAccount, ['route' => ['admin.finances.program-accounts.transactions.store', [$programAccount, 'type' => request()->type]], 'method' => 'POST']) !!}

<div class="row">
    {{-- Amount --}}
    <div class="form-group col-6">
      {{ Form::label('this_account', request()->type == 'deposit' ? __('To Account') : __('From Account'), ['class' => 'col-form-label']) }}
      {!! Form::text('this_account', $programAccount->name, ['class' => 'form-control', 'placeholder' => __('Account'), 'disabled']) !!}
    </div>

    <div class="form-group col-6">
      {{ Form::label('account_id', request()->type != 'deposit' ? __('To Account') : __('From Account'), ['class' => 'col-form-label']) }}
      {!! Form::select('account_id', [], null, ['class' => 'form-control globalOfSelect2Remote',
      'data-url' => route('resource-select', ['resource' => 'AccountBalance', 'except' => $programAccount->id]),
      'placeholer' => __('Select Account')]) !!}
    </div>
    {{-- Amount --}}
    <div class="form-group col-6">
        {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('Amount')]) !!}
    </div>
    {{-- description --}}
    <div class="form-group col-12">
        {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'rows' => '3']) !!}
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