 <div class="offcanvas-header">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Edit Email Account</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
      <form class="add-new-user pt-0" id="add-mail-account">
      <div class="mb-3">
          <label class="form-label" for="connection_type">Account Type</label>
          <select id="connection_type" disabled name="connection_type" class="select2 form-select">
            <option selected value="{{$account->connection_type}}">{{$account->connection_type}}</option>
          </select>
        </div>
    <div id="imap-area" style="filter:blur(4px);">
        <div class="mb-3">
          <label class="form-label" for="add-user-email">Email Address</label>
          <input type="email" id="add-user-email" value="{{$account->email}}" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email" />
        </div>
        <div class="mb-3">
        <div class="form-check">
            <input name="create_contact" class="form-check-input" checked="$account->create_contact" type="checkbox" value="" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
            Create Contact record if record does not exists.
            </label>
          </div>
        </div>
@if($account->connection_type=='Imap')
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" class="form-control" name="password" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="username">Username(Optional)</label>
          <input type="text" id="username" class="form-control" name="username" />
        </div>
        <h5 class="mb-3 font-medium text-neutral-700 dark:text-neutral-100">Incomming Mail (Imap)</h5>
        <div class="mb-3">
          <label class="form-label" for="imap_server">Server</label>
          <input type="text" id="imap_server" class="form-control" name="imap_server" />
        </div>
<div class="row">
    <div class="col-md-5">
    <div class="mb-3">
    <label class="form-label" for="imap_port">Port</label>
          <input type="number" value="993" id="imap_port" class="form-control" name="imap_port" />
          </div>
    </div>
          <div class="col-md-7">
        <div class="mb-3">
<label class="form-label" for="imap_encryption">Encryption</label>
          <select id="imap_encryption" class="form-select">
            <option value="ssl">ssl</option>
            <option value="tls">tls</option>
            <option value="starttls">starttls</option>
</select>
</div>
</div>
        </div>
        <h5 class="mb-3 font-medium text-neutral-700 dark:text-neutral-100">Outgoing Mail (SMTP)</h5>
        <div class="mb-3">
          <label class="form-label" for="smtp_server">Server</label>
          <input type="text" id="smtp_server" class="form-control" name="smtp_server" />
        </div>

<div class="row">
    <div class="col-md-5">
    <div class="mb-3">
<label class="form-label" for="smtp_port">Port</label>
          <input type="number" value="465" id="smtp_port" class="form-control" name="smtp_port" />
          </div>
    </div>
          <div class="col-md-7">  
          <div class="mb-3">
          <label class="form-label" for="smtp_encryption">Encryption</label>
          <select id="smtp_encryption" class="form-select">
            <option value="ssl">ssl</option>
            <option value="tls">tls</option>
            <option value="starttls">starttls</option>
</select>
</div>
</div>
        </div>
@endif
        <div class="mb-3">
        <div class="form-check">
            <input name="validate_cert" class="form-check-input" type="checkbox" value="0" id="validate_cert">
            <label class="form-check-label" for="validate_cert">
            Allow non secure certificate.
            </label>
          </div>
        </div>
        <h5 class="mb-3 font-medium text-neutral-700 dark:text-neutral-100">From Header</h5>
        <div class="mb-3">
          <label class="form-label" for="from_name_header">From Name</label>
          <input type="text" id="from_name_header" class="form-control" name="from_name_header" />
        </div>

    </div>
    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
    <button type="button" id="test-connection" class="btn btn-primary me-sm-3 me-1 data-submit">Test Connection / Retrieve Folders</button>
        <button type="button" id="save-account" class="btn btn-primary me-sm-3 me-1 data-submit">Connect Account</button>
      </form>
    </div>