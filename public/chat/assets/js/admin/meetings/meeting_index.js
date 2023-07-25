(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else {
		var a = factory();
		for(var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
	}
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./Modules/Chat/Resources/assets/js/admin/meetings/meeting_index.js":
/*!**************************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/admin/meetings/meeting_index.js ***!
  \**************************************************************************/
/***/ (function() {



var _this = this;
$(document).ready(function () {
  var tbl = $('#meetingsTable').DataTable({
    processing: true,
    serverSide: true,
    'bStateSave': true,
    'order': [[1, 'desc']],
    ajax: {
      url: route('meetings.index')
    },
    columnDefs: [{
      'targets': [1],
      'className': 'text-center start-date-width'
    }, {
      'targets': [2],
      'className': 'text-center',
      'width': '10%'
    }, {
      'targets': [3],
      'orderable': false,
      'width': '15%',
      'className': 'text-center'
    }, {
      'targets': [4],
      'orderable': false,
      'className': 'text-center',
      'width': '100px'
    }, {
      'targets': [5],
      'orderable': false,
      'className': 'text-center',
      'width': '150px'
    }],
    columns: [{
      data: 'topic',
      name: 'topic'
    }, {
      data: function data(row) {
        return moment(row.start_time).locale(currentLocale).format('Do MMM, YYYY hh:mm A');
      },
      name: 'start_time'
    }, {
      data: function data(row) {
        return "".concat(row.duration, " minutes");
      },
      name: 'duration'
    }, {
      data: function data(row) {
        return "<select class=\"statusDrp\" data-id=\"".concat(row.id, "\">") + "<option value=\"1\" ".concat(row.status == 1 ? 'selected' : '', ">Awaited</option><option value=\"2\" ").concat(row.status == 2 ? 'selected' : '', ">Finished</option>") + "</select>";
      },
      name: 'status'
    }, {
      data: 'password',
      name: 'password'
    }, {
      data: function data(row) {
        var startBtn = '<a href="' + row.meta.start_url + '" target="_blank" class="btn btn-primary btn-sm m-1 zoom-video"><i class="fa fa-video-camera"></i></a>';
        var editBtn = '<a title="Edit" class="index__btn btn btn-ghost-success btn-sm edit-btn mr-1" href="' + route('meetings.edit', row.id) + '">' + '<i class="cui-pencil action-icon"></i>' + '</a>';
        startBtn = row.status == 1 ? startBtn + editBtn : '';
        return '<div class="d-flex justify-content-center align-items-center">' + startBtn + '<button title="Delete" class="index__btn btn btn-ghost-danger btn-sm delete-btn" data-id="' + row.id + '">' + '<i class="cui-trash action-icon"></i></button> </div>';
      },
      name: 'id',
      'searchable': false
    }],
    drawCallback: function drawCallback() {
      this.api().state.clear();
      $('.statusDrp').select2({
        width: '100%',
        minimumResultsForSearch: -1,
        placeholder: Lang.get('messages.placeholder.select_member')
      });
    }
  });
  $(document).on('change', '.statusDrp', function () {
    var status = $(this).val();
    var meetingId = $(this).data('id');
    $.ajax({
      url: route('meeting.change-meeting-status', {
        meeting: meetingId,
        status: status
      }),
      type: 'GET',
      success: function success(obj) {
        if (obj.success) {
          displayToastr('Success', 'success', obj.message);
          tbl.ajax.reload();
        }
      },
      error: function error(obj) {
        displayToastr('Error', 'error', obj.responseJSON.message);
      }
    });
  });
});
var deleteBtn;
var swalDelete = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-danger mr-2 btn-lg',
    cancelButton: 'btn btn-secondary btn-lg'
  },
  buttonsStyling: false
});
function deleteItem(url, tableId, header) {
  var callFunction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
  swalDelete.fire({
    title: Lang.get('messages.placeholder.are_you_sure'),
    html: Lang.get('messages.placeholder.want_to_delete_this') + '"' + header + '"' + ' ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    input: 'text',
    inputPlaceholder: Lang.get('messages.placeholder.write_delete'),
    inputValidator: function inputValidator(value) {
      if (value !== "delete") {
        return Lang.get('messages.placeholder.need_to_write_delete');
      }
    }
  }).then(function (result) {
    if (result.value) {
      deleteBtn.addClass('invisible');
      deleteItemAjax(url, tableId, header, callFunction = null);
    }
  });
}

// open delete confirmation model
$(document).on('click', '.delete-btn', function (event) {
  var meetingId = $(this).data('id');
  deleteBtn = $(this);
  deleteItem(route('meetings.destroy', meetingId), '#meetingsTable', Lang.get('messages.placeholder.meeting'));
});
setTimeout(function () {
  $('.alert').slideUp(function () {
    $(_this).addClass('d-none');
  });
}, 1500);

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./Modules/Chat/Resources/assets/js/admin/meetings/meeting_index.js"]();
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});