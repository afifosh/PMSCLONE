{!! Form::model($changeRequest, ['route' => ['admin.contracts.change-requests.reviews.store', ['change_request' => $changeRequest, 'contract' => $changeRequest->contract_id]], 'method' => 'POST']) !!}

<div class="row">
    {{-- reviewed At --}}
    <div class="col-12 mb-2">
        <div class="form-group">
            {!! Form::label('reviewed_at', __('Reviewed At'), ['class' => 'col-form-label']) !!}
            {!! Form::date('reviewed_at', null, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"minDate": "'. $changeRequest->requested_at .'"}', 'placeholder' => __('Reviewed At')]) !!}
        </div>
    </div>

    {{-- status --}}
    <div class="col-12 mb-2">
        <div class="form-group">
            {!! Form::label('status', __('Status'), ['class' => 'col-form-label']) !!}
            {!! Form::select('status', ['Approved' => 'Approved', 'Rejected' => 'Rejected'], null, ['class' => 'form-control globalOfSelect2']) !!}
        </div>
    </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
