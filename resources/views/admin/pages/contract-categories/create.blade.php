@if ($contract_category->id)
    {!! Form::model($contract_category, ['route' => ['admin.contract-categories.update', $contract_category->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($contract_category, ['route' => ['admin.contract-categories.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- Subject --}}
    <div class="form-group col-12">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Name')]) !!}
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
