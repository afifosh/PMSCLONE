<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div>
          <div class="text-center mb-4">
            <h3 class="role-title mb-2">Add New Role</h3>
            <p class="text-muted">Set role permissions</p>
          </div>
          <!-- Add role form -->
          <form id="addRoleForm" class="row g-3" action="{{route('admin.roles.store')}}" method="POST">
            @csrf
            <div class="col-12 mb-4">
              <label class="form-label" for="modalRoleName">Role Name</label>
              <input type="text" id="modalRoleName" name="role" class="form-control" placeholder="Enter a role name" tabindex="-1" required />
            </div>
            <div class="col-12">
              <h5>Role Permissions</h5>
              <!-- Permission table -->
              <div class="table-responsive">
                <table class="table table-flush-spacing">
                  <tbody class="permissions-body">
                  </tbody>
                </table>
              </div>
              <!-- Permission table -->
            </div>
            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
          </form>
          <!--/ Add role form -->
        </div>
      </div>
    </div>
  </div>
</div>
