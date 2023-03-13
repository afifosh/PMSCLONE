<div class="row">
    <div class="col-md-6 mb-4">
        <label for="sesHostName" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Host name')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="sesHostName" placeholder="@lang('Type host name')" aria-describedby="sesHostName" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesAccessKeyId" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Access key id')
        </label>
        <input value="{{ $settings['access_key_id'] ?? '' }}" name="access_key_id" type="text" class="form-control" id="sesAccessKeyId" placeholder="@lang('Type access key id')" aria-describedby="sesAccessKeyId" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesSecretAccessKey" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Secret access key')
        </label>
        <input value="{{ $settings['secret_access_key'] ?? '' }}" name="secret_access_key" type="text" class="form-control" id="sesSecretAccessKey" placeholder="@lang('Type secret access key')" aria-describedby="sesSecretAccessKey" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="sesRegion" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Region')
        </label>
        <input value="{{ $settings['region'] ?? '' }}" name="region" type="text" class="form-control" id="sesRegion" placeholder="@lang('Type region')" aria-describedby="sesRegion" />
    </div>
</div>