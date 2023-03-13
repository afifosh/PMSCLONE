<div id="mailtrapService" class="row">
    <div class="col-md-6 mb-4">
        <label for="mailtrapUsername" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Username')
        </label>
        <input value="{{ $settings['username'] ?? '' }}" name="username" type="text" class="form-control" id="mailtrapUsername" placeholder="@lang('Type username')" aria-describedby="mailtrapUsername" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrapHost" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Mailtrap host')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="mailtrapHost" placeholder="@lang('Type mailtrap host')" aria-describedby="mailtrapHost" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrapPort" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Port')
        </label>
        <input value="{{ $settings['port'] ?? '' }}" name="port" type="text" class="form-control" id="mailtrapPort" placeholder="@lang('Type mailtrap port')" aria-describedby="mailtrapPort" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrapPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Password to access')
        </label>
        <input value="{{ $settings['password'] ?? '' }}" name="password" type="password" class="form-control" id="mailtrapPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="mailtrapPasswordToAccess" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrapEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Encryption type')
        </label>
        <select name="encryption" id="mailtrapEncryptionKey" class="selectpicker w-100" data-style="btn-default">
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