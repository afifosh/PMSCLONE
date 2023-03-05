<!-- ses email service fields -->
<div id="sesEmailService" class="row email-service {{ $active_service->name === 'ses' ? '' : 'd-none' }}">
    <div class="col-md-6 mb-4">
        <label for="sesHostName" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Host name')
        </label>
        <input name="ses_host" value="{{ $ses->host ?? '' }}" type="text" class="form-control" id="sesHostName" placeholder="@lang('Type host name')" aria-describedby="sesHostName" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesAccessKeyId" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Access key id')
        </label>
        <input name="ses_access_key_id" value="{{ $ses->access_key_id ?? '' }}" type="text" class="form-control" id="sesAccessKeyId" placeholder="@lang('Type access key id')" aria-describedby="sesAccessKeyId" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesSecretAccessKey" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Secret access key')
        </label>
        <input name="ses_secret_access_key" value="{{ $ses->secret_access_key ?? '' }}" type="text" class="form-control" id="sesSecretAccessKey" placeholder="@lang('Type secret access key')" aria-describedby="sesSecretAccessKey" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesRegion" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Region')
        </label>
        <input name="ses_region" value="{{ $ses->region ?? '' }}" type="text" class="form-control" id="sesRegion" placeholder="@lang('Type region')" aria-describedby="sesRegion" />
    </div>
</div>