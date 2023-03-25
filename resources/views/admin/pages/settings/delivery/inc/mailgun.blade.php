<!-- Mailgun email service fields -->
<div class="row">
    <div class="col-md-6 mb-4">
        <label for="mailgun_domain_name" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Domain name')
        </label>
        <input value="{{ $settings['domain_name'] ?? '' }}" name="domain_name" type="text" class="form-control" id="mailgun_domain_name" placeholder="@lang('Type domain name')" aria-describedby="mailgun_domain_name" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailgun_api_key" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Api key')
        </label>
        <input value="{{ $settings['api_key'] ?? '' }}" name="api_key" type="text" class="form-control" id="mailgun_api_key" placeholder="@lang('Type api key')" aria-describedby="mailgun_api_key" />
    </div>
</div>