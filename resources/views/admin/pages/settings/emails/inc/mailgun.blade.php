<!-- Mailgun email service fields -->
<div id="mailgunEmailService" class="row email-service {{ $active_service->name === 'mailgun' ? 'd-block' : 'd-none' }}">
    <div class="col-md-6 mb-4">
        <label for="mailgunDomainName" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Domain name')
        </label>
        <input name="mailgun_domain_name" value="{{ $mailgun->domain_name ?? '' }}" type="text" class="form-control" id="mailgunDomainName" placeholder="@lang('Type domain name')" aria-describedby="mailgunDomainName" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailgunApiKey" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Api key')
        </label>
        <input name="mailgun_api_key" value="{{ $mailgun->api_key ?? '' }}" type="text" class="form-control" id="mailgunApiKey" placeholder="@lang('Type api key')" aria-describedby="mailgunApiKey" />
    </div>
</div>