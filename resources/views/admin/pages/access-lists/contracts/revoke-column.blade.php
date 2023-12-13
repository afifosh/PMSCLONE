<div class="">
  <label class="switch">
    <input class="switch-input" name="revoke_access" onclick="revokeContractAccess({{$admin_id}}, {{$contract->id}}, {{!$aclRule->is_revoked}})" type="checkbox" @checked($aclRule->is_revoked) value="1">
    <span class="switch-toggle-slider">
      <span class="switch-on"></span>
      <span class="switch-off"></span>
    </span>
    <span class="switch-label"></span>
  </label>
</div>
