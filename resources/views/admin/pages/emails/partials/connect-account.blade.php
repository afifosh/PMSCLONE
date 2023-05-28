 <!-- Offcanvas to add new user -->
 <div class="offcanvas offcanvas-xxl offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="offcanvasAddUser" style="width:50%; background-color:white !important;overflow-y:auto" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header" style="display:flex">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Create Email Account</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100" style="padding:20px; width:100%; display:block;">
      <form class="add-new-user pt-0" id="add-mail-account">
      <div class="mb-3">
          <label class="form-label" for="connection_type">Account Type</label>
          <select id="connection_type" name="connection_type" class="select2 form-select">
            <option value="">Select</option>
            <option value="Outlook">Outlook</option>
            <option value="Gmail">Gmail</option>
            <option value="Imap">Imap</option>
          </select>
        </div>
      <div class="mb-3">
      <label class="form-label d-block" for="initial_sync_from">Sync emails from</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input initial_sync_from" checked type="radio" name="initial_sync_from" id="initial_sync_from1" value="{{date('Y-m-d H:i:s')}}">
            <label class="form-check-label" for="initial_sync_from1">Now</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input initial_sync_from" type="radio" name="initial_sync_from" id="initial_sync_from2" value="{{date('Y-m-d H:i:s', strtotime('-1 month', strtotime(date('Y-m-d H:i:s'))))}}">
            <label class="form-check-label" for="initial_sync_from1">1 month ago</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input initial_sync_from" type="radio" name="initial_sync_from" id="initial_sync_from3" value="{{date('Y-m-d H:i:s', strtotime('-3 month', strtotime(date('Y-m-d H:i:s'))))}}">
            <label class="form-check-label" for="initial_sync_from3">3 months ago</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input initial_sync_from" type="radio" name="initial_sync_from" id="initial_sync_from4" value="{{date('Y-m-d H:i:s', strtotime('-6 month', strtotime(date('Y-m-d H:i:s'))))}}">
            <label class="form-check-label" for="initial_sync_from4">6 months ago</label>
          </div>
    </div>
    <div id="imap-area" style="filter:blur(4px); padding-bottom:30px;">
        <div class="mb-3">
          <label class="form-label" for="add-user-email">Email Address</label>
          <input type="email" value="mail@afifjabado.com" id="add-user-email" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email" />
        </div>
        <div class="mb-3">
        <div class="form-check">
            <input name="create_contact" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
            Create Contact record if record does not exists.
            </label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" class="form-control" value="GOcXK7~wfU$w" name="password" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="username">Username(Optional)</label>
          <input type="text" id="username" class="form-control" name="username" />
        </div>
        <h5 class="mb-3 font-medium text-neutral-700 dark:text-neutral-100">Incomming Mail (Imap)</h5>
        <div class="mb-3">
          <label class="form-label" for="imap_server">Server</label>
          <input type="text" id="imap_server" class="form-control" value="mail.afifjabado.com" name="imap_server" />
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
          <select id="imap_encryption" name="imap_encryption" class="form-select">
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
          <input type="text" id="smtp_server" class="form-control" value="mail.afifjabado.com" name="smtp_server" />
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
          <select id="smtp_encryption" name="smtp_encryption" class="form-select">
            <option value="ssl">ssl</option>
            <option value="tls">tls</option>
            <option value="starttls">starttls</option>
</select>
</div>
</div>
        </div>
        <div class="mb-3">
        <div class="form-check">
            <input name="validate_cert" class="form-check-input" type="checkbox" value="1" id="validate_cert">
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
      <div class="mb-3" id="folders-area">

      </div>
      <div id="errors" style="display:none" class="mb-3 alert alert-danger">

      </div>
    </div>
    <div style="padding-top:30px; border-top:1px solid #ddd">
    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
    <button type="button" id="test-connection" style="display:none !important" class="btn btn-primary me-sm-3 me-1 data-submit">Test Connection / Retrieve Folders</button>
        <button type="button" id="save-account" class="btn btn-primary me-sm-3 me-1 data-submit">Connect Account</button>
        </div>
      </form>
    </div>
  </div>
