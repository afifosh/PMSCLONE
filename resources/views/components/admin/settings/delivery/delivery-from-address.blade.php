@props([
    'identifier' => 'amazon_sesService',
    'settings' => [],
])
<div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="from_name-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from name')
            </label>
            <input value="{{ $settings['from_name'] ?? '' }}" name="from_name" type="text" class="form-control" id="from_name-{{$identifier}}" placeholder="John Doe" aria-describedby="from_name-{{$identifier}}" />
        </div>

        <div class="col-md-6 mb-4">
            <label for="from_email-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from email')
            </label>
            <input value="{{ $settings['from_email'] ?? '' }}" name="from_email" type="email" class="form-control" id="from_email-{{$identifier}}" placeholder="@lang('Type email from address')" aria-describedby="from_email-{{$identifier}}" />
        </div>
    </div>
</div>