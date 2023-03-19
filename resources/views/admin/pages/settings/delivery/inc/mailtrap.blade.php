<div id="mailtrapService" class="row">
    <div class="col-md-6 mb-4">
        <label for="mailtrap_username" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Username')
        </label>
        <input value="{{ $settings['username'] ?? '' }}" name="username" type="text" class="form-control" id="mailtrap_username" placeholder="@lang('Type username')" aria-describedby="mailtrap_username" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrap_host" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Mailtrap host')
        </label>
        <input value="{{ $settings['host'] ?? '' }}" name="host" type="text" class="form-control" id="mailtrap_host" placeholder="@lang('Type mailtrap host')" aria-describedby="mailtrap_host" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrap_port" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Port')
        </label>
        <input value="{{ $settings['port'] ?? '' }}" name="port" type="text" class="form-control" id="mailtrap_port" placeholder="@lang('Type mailtrap port')" aria-describedby="mailtrap_port" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrap_password" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Password to access')
        </label>
        <input value="{{ $settings['password'] ?? '' }}" name="password" type="password" class="form-control" id="mailtrap_password" placeholder="@lang('Type password to access')" aria-describedby="mailtrap_password" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="mailtrap_encryption" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Encryption type')
        </label>
        <select name="encryption" id="mailtrap_encryption" class="selectpicker w-100" data-style="btn-default">
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