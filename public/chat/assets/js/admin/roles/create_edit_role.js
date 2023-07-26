/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/admin/roles/create_edit_role.js ***!
  \**************************************************************************/
$('#createRoleForm').on('submit', function (event) {
  event.preventDefault();
  var name = $('#role_name').val();
  var emptyName = name.trim().replace(/ \r\n\t/g, '') === '';
  if (emptyName) {
    displayToastr('Error', 'error', 'Name field is not contain only white space');
    return;
  }
  var loadingButton = jQuery(this).find('#btnCreateRole');
  loadingButton.button('loading');
  $('#createRoleForm')[0].submit();
  return true;
});
$('#editRoleForm').on('submit', function (event) {
  event.preventDefault();
  var editName = $('#edit_role_name').val();
  var emptyEditName = editName.trim().replace(/ \r\n\t/g, '') === '';
  if (emptyEditName) {
    displayToastr('Error', 'error', 'Name field is not contain only white space');
    return;
  }
  var loadingButton = jQuery(this).find('#btnEditSave');
  loadingButton.button('loading');
  $('#editRoleForm')[0].submit();
  return true;
});
/******/ })()
;