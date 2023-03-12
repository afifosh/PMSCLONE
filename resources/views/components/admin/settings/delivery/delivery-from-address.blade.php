@props([
    'identifier' => 'amazon_sesService',
])
<div>
    <div class="row">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            {!! implode('<br />', $errors->all('<span>:message</span>')) !!}
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="fromName-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from name')
            </label>
            <input name="from_name" type="text" class="form-control" id="fromName-{{$identifier}}" placeholder="John Doe" aria-describedby="fromName-{{$identifier}}" />
        </div>

        <div class="col-md-6 mb-4">
            <label for="fromEmail-{{$identifier}}" class="form-label fs-6 mb-2 fw-semibold">
                @lang('Email sent from email')
            </label>
            <input name="from_email" type="email" class="form-control" id="fromEmail-{{$identifier}}" placeholder="@lang('Type email from address')" aria-describedby="fromEmail-{{$identifier}}" />
        </div>
    </div>
</div>