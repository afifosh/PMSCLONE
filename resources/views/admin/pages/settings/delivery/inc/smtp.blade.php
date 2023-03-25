<div class="row">
    <div class="col-md-6 mb-4">
        <label for="smtp_username" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Username')
        </label>
        <input value="{{ $settings['username'] ?? '' }}" name="username" type="text" class="form-control" id="smtp_username" placeholder="@lang('Type username')" aria-describedby="smtp_username" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtp_host" class="form-label fs-6 mb-2 fw-semibold">
            @lang('SMTP host')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="smtp_host" placeholder="@lang('Type SMTP host')" aria-describedby="smtp_host" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtp_port" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Port')
        </label>
        <input value="{{ $settings['port'] ?? '' }}" name="port" type="text" class="form-control" id="smtp_port" placeholder="@lang('Type SMTP port')" aria-describedby="smtp_port" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtp_password" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Password to access')
        </label>
        <input value="{{ $settings['password'] ?? '' }}" name="password" type="password" class="form-control" id="smtp_password" placeholder="@lang('Type password to access')" aria-describedby="smtp_password" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtp_encryption" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Encryption type')
        </label>
        <select name="encryption" id="smtp_encryption" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
            <option value="">@lang('Choose one')</option>
            <option value="tls" {{ isset($settings['encryption']) && $settings['encryption'] === 'tls' ? 'selected' : '' }}>
                @lang('TLS')
            </option>
            <option value="ssl" {{ isset($settings['encryption']) && $settings['encryption'] === 'ssl' ? 'selected' : '' }}>
                @lang('SSL')
            </option>
        </select>
    </div>
</div>