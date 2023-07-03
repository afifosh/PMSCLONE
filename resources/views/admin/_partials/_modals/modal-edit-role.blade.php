<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div>
          <div class="text-center mb-4">
            <h3 class="role-title mb-2">Edit Role</h3>
            <p class="text-muted">Set role permissions</p>
          </div>
          <!-- Edit role form -->
          <form id="editRoleForm" class="row g-3" action="" method="POST">
            @method('PUT')
            @csrf
            <div class="edit-role-data">

            </div>
            <div class="d-flex justify-content-end">
              <button type="reset" class="btn btn-label-secondary me-1" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>
          <!--/ Edit role form -->
        </div>
      </div>
    </div>
  </div>
</div>
