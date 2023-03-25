@props([
    'identifier' => 'amazon_sesService',
    'settings' => [],
])
<div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="fromName-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from name')
            </label>
            <input value="{{ $settings['from_name'] ?? '' }}" name="from_name" type="text" class="form-control" id="fromName-{{$identifier}}" placeholder="John Doe" aria-describedby="fromName-{{$identifier}}" />
        </div>

        <div class="col-md-6 mb-4">
            <label for="fromEmail-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from email')
            </label>
            <input value="{{ $settings['from_email'] ?? '' }}" name="from_email" type="email" class="form-control" id="fromEmail-{{$identifier}}" placeholder="@lang('Type email from address')" aria-describedby="fromEmail-{{$identifier}}" />
        </div>
    </div>
</div>