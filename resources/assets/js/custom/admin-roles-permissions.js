'use strict';

function addRole(){
  axios.get('roles/create').then(function(response){
    $('#addRoleModal').find('.permissions-body').html(response.data);
    $('#addRoleModal').modal('show');
    });
}
$(function () {
  $('.open-role-edit-modal').on('click', function (e) {
    var role = $(this).data('role')
    axios.get(`roles/${role}/edit`).then(function(response){
      $('#editRoleModal').find('.edit-role-data').html(response.data);
      $("#editRoleForm").attr('action', 'roles/' + role);
      $('#editRoleModal').modal('show');
      });
  });
});
