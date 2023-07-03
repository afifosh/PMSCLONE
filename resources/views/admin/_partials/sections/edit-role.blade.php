<!-- Add Role Modal -->
<div class="col-12 mb-4">
  <label class="form-label" for="modalRoleName">Role Name</label>
  <input type="text" id="modalRoleName" name="role" class="form-control" value="{{$role->name}}" placeholder="Enter a role name" tabindex="-1" required />
</div>
<div class="col-12">
  <h5>Role Permissions</h5>
  <!-- Permission table -->
  <div class="table-responsive">
    <table class="table table-flush-spacing">
      <tbody class="permissions-body">
        @forelse ($modules as $module)
        <tr>
          <td class="text-nowrap fw-semibold">{{$module->name}}</td>
          <td>
            <div class="d-flex">
              @forelse ($module->permissions as $permission)
                <div class="form-check {{$loop->last ? '' : 'me-3 me-lg-5'}} ">
                  <input class="form-check-input" name="permissions[]" type="checkbox" value="{{$permission->id}}" id="persm-{{$permission->id}}" {{in_array($permission->id, $allowed_permissions) ? 'checked' : ''}} />
                  <label class="form-check-label" for="persm-{{$permission->id}}">
                    @if($permission->name == 'Personal Mailbox')
                      Personal Account
                    @elseif ($permission->name == 'access shared inbox')
                      Shared Account
                    @else
                    {{explode(" ",$permission->name)[0]}}
                    @endif
                  </label>
                </div>
              @empty
              @endforelse
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
  <!-- Permission table -->
</div>
<!--/ Add Role Modal -->
