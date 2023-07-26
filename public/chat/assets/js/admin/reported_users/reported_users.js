/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/admin/reported_users/reported_users.js ***!
  \*********************************************************************************/
$(document).ready(function () {
  $('#isActiveFilter').select2({
    minimumResultsForSearch: -1
  });
  var tbl = $('#reportedUsersTable').DataTable({
    processing: true,
    serverSide: true,
    'bStateSave': true,
    'order': [[2, 'desc']],
    ajax: {
      url: route('reported-users.index'),
      data: function data(_data) {
        _data.is_active_filter = $('#isActiveFilter').find('option:selected').val();
      }
    },
    columnDefs: [{
      'targets': [4],
      'orderable': false,
      'className': 'text-center',
      'width': '7%'
    }, {
      'targets': [3],
      'orderable': false,
      'className': 'text-center',
      'width': '80px'
    }, {
      'targets': [2],
      'width': '100px'
    }],
    columns: [{
      data: function data(_data2) {
        return htmlSpecialCharsDecode(_data2.reported_by.name);
      },
      name: 'reportedBy.name'
    }, {
      data: function data(_data3) {
        return htmlSpecialCharsDecode(_data3.reported_to.name);
      },
      name: 'reportedTo.name'
    }, {
      data: function data(row) {
        return row;
      },
      render: function render(row) {
        return '<span data-toggle="tooltip" title="' + moment(row.created_at).locale(currentLocale).format('hh:mm:ss a') + '">' + moment(row.created_at).locale(currentLocale).format('DD-MMM-YYYY') + '</span>';
      },
      name: 'created_at'
    }, {
      data: function data(row) {
        if (row.reported_to.id == loggedInUserId) {
          return row.reported_to.is_active ? 'Active' : 'Deactive';
        }
        row.checked = row.reported_to.is_active === 0 ? '' : 'checked';
        return $.templates('#isActiveSwitch').render(row);
      },
      name: 'id'
    }, {
      data: function data(row) {
        return $.templates('#viewDelIcons').render(row);
      },
      name: 'id'
    }],
    drawCallback: function drawCallback() {
      this.api().state.clear();
      $('[data-toggle="tooltip"]').tooltip();
    },
    'fnInitComplete': function fnInitComplete() {
      $('#isActiveFilter').change(function () {
        tbl.ajax.reload();
      });
    }
  });
  var swalDelete = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-danger mr-2 btn-lg',
      cancelButton: 'btn btn-secondary btn-lg'
    },
    buttonsStyling: false
  });

  // open delete confirmation model
  $(document).on('click', '.delete-btn', function () {
    var reportedUsersId = $(this).data('id');
    // let deleteReportedUsersUrl = route('reported-users.destroy',reportedUsersId);
    deleteItem(route('reported-users.destroy', reportedUsersId), '#reportedUsersTable', Lang.get('messages.placeholder.reported_user'));
  });
  $(document).on('click', '.view-btn', function () {
    var reportId = $(this).data('id');
    // let viewReportedUsersUrl = route('reported-users.show',reportId);

    $.ajax({
      type: 'GET',
      url: route('reported-users.show', reportId),
      success: function success(data) {
        $('.reported-user-notes').html(data.notes);
        $('.reported-by').text(data.reported_by.name);
        $('.reported-to').text(data.reported_to.name);
        $('#viewReportNoteModal').modal('show');
      }
    });
  });
  function deleteItem(url, tableId, header) {
    var callFunction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    swalDelete.fire({
      title: Lang.get('messages.placeholder.are_you_sure'),
      html: Lang.get('messages.placeholder.want_to_delete_this') + '"' + header + '"' + ' ?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete'
    }).then(function (result) {
      if (result.value) {
        deleteItemAjax(url, tableId, header, callFunction = null);
      }
    });
  }

  // listen user activation deactivation change event
  $(document).on('change', '.is-active', function (event) {
    var userId = $(event.currentTarget).data('id');
    activeDeActiveUser(userId);
  });

  // activate de-activate user
  window.activeDeActiveUser = function (id) {
    $.ajax({
      url: route('active-de-active-user', id),
      method: 'post',
      cache: false,
      success: function success(result) {
        if (result.success) {
          displayToastr('Success', 'success', result.message);
          tbl.ajax.reload(null, false);
        }
      },
      error: function error(_error) {
        displayToastr('Error', 'error', _error.responseJSON.message);
      }
    });
  };
});
/******/ })()
;