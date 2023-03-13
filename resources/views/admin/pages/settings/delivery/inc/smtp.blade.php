<div class="row">
    <div class="col-md-6 mb-4">
        <label for="smtpUsername" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Username')
        </label>
        <input value="{{ $settings['username'] ?? '' }}" name="username" type="text" class="form-control" id="smtpUsername" placeholder="@lang('Type username')" aria-describedby="smtpUsername" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpHost" class="form-label fs-6 mb-2 fw-semibold">
            @lang('SMTP host')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="smtpHost" placeholder="@lang('Type SMTP host')" aria-describedby="smtpHost" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpPort" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Port')
        </label>
        <input value="{{ $settings['port'] ?? '' }}" name="port" type="text" class="form-control" id="smtpPort" placeholder="@lang('Type SMTP port')" aria-describedby="smtpPort" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpPassword" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Password to access')
        </label>
        <input value="{{ $settings['password'] ?? '' }}" name="password" type="password" class="form-control" id="smtpPassword" placeholder="@lang('Type password to access')" aria-describedby="smtpPassword" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpEncryptionType" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Encryption type')
        </label>
        <select name="encryption" id="smtpEncryptionType" class="selectpicker w-100" data-style="btn-default">
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