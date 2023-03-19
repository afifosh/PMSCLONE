<div class="row">
    <div class="col-md-6 mb-4">
        <label for="ses_host" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Host name')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="ses_host" placeholder="@lang('Type host name')" aria-describedby="ses_host" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="ses_access_key_id" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Access key id')
        </label>
        <input value="{{ $settings['access_key_id'] ?? '' }}" name="access_key_id" type="text" class="form-control" id="ses_access_key_id" placeholder="@lang('Type access key id')" aria-describedby="ses_access_key_id" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="ses_secret_access_key" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Secret access key')
        </label>
        <input value="{{ $settings['secret_access_key'] ?? '' }}" name="secret_access_key" type="text" class="form-control" id="ses_secret_access_key" placeholder="@lang('Type secret access key')" aria-describedby="ses_secret_access_key" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="ses_region" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Region')
        </label>
        <input value="{{ $settings['region'] ?? '' }}" name="region" type="text" class="form-control" id="ses_region" placeholder="@lang('Type region')" aria-describedby="ses_region" />
    </div>
</div>