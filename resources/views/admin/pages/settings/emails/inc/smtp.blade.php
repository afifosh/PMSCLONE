<!-- Mailgun email service fields -->
<div id="smtpEmailService" class="row email-service {{ $active_service->name === 'smtp' ? '' : 'd-none' }}">
    <div class="col-md-6 mb-4">
        <label for="smtpUsername" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Username')
        </label>
        <input name="smtp_username" value="{{ $smtp->username ?? '' }}" type="text" class="form-control" id="smtpUsername" placeholder="@lang('Type username')" aria-describedby="smtpUsername" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpHost" class="form-label fs-6 mb-2 fw-semibold">
            @lang('SMTP host')
        </label>
        <input name="smtp_host" value="{{ $smtp->host ?? '' }}" type="text" class="form-control" id="smtpHost" placeholder="@lang('Type SMTP host')" aria-describedby="smtpHost" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpPort" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Port')
        </label>
        <input name="smtp_port" value="{{ $smtp->port ?? '' }}" type="text" class="form-control" id="smtpPort" placeholder="@lang('Type SMTP port')" aria-describedby="smtpPort" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Password to access')
        </label>
        <input name="smtp_password" value="{{ $smtp->password ?? '' }}" type="password" class="form-control" id="smtpPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="smtpPasswordToAccess" />
    </div>

    <div class="col-md-6 mb-4">
        <label for="smtpEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
            @lang('Encryption type')
        </label>
        <select name="smtp_encryption" value="{{ $smtp->encryption ?? '' }}" id="smtpEncryptionKey" class="selectpicker w-100" data-style="btn-default">
            <option value="">@lang('Choose one')</option>
            <option value="tls" {{ isset($smtp->encryption) && $smtp->encryption === 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
            <option value="ssl" {{ isset($smtp->encryption) && $smtp->encryption === 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
        </select>
    </div>
</div>