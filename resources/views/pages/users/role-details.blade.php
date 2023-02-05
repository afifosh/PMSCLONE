<!-- Add Role Modal -->
<div class="col-12 mb-4">
  <label class="form-label" for="modalRoleName">Role Name</label>
  <input type="text" id="modalRoleName" name="role" class="form-control" value="{{$role->name}}" tabindex="-1" disabled />
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
                  <input class="form-check-input" type="checkbox" id="persm-{{$permission->id}}" {{$true_all || in_array($permission->id, $allowed_permissions) ? 'checked' : ''}} disabled/>
                  <label class="form-check-label" for="persm-{{$permission->id}}">
                    {{explode(" ",$permission->name)[0]}}
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
