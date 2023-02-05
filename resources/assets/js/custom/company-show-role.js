$(function () {
  $('.open-show-role-modal').on('click', function (e) {
    var role = $(this).data('role')
    axios.get(`users/roles/${role}`).then(function(response){
      $('#showRoleModal').find('.show-role-data').html(response.data);
      $('#showRoleModal').modal('show');
      });
  });
});
