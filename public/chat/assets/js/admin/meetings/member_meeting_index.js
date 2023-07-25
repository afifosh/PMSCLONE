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
var __webpack_exports__ = {};
/*!*********************************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/admin/meetings/member_meeting_index.js ***!
  \*********************************************************************************/


$(document).ready(function () {
  var tbl = $('#memberMeetingTable').DataTable({
    processing: true,
    serverSide: true,
    'bStateSave': true,
    'order': [[0, 'asc']],
    ajax: {
      url: route('meetings.member_index'),
      data: function data(_data) {
        _data.filter_user = $('#filter_user').find('option:selected').val();
      }
    },
    columnDefs: [{
      'targets': [1],
      'className': 'text-center',
      'width': '20%'
    }, {
      'targets': [2, 4, 3],
      'className': 'text-center',
      'width': '70px'
    }, {
      'targets': [5],
      'orderable': false,
      'className': 'text-center',
      'width': '100px'
    }],
    columns: [{
      data: 'topic',
      name: 'topic'
    }, {
      data: function data(row) {
        return moment(row.start_time).locale(currentLocale).format('Do MMM, YYYY hh:mm A');
      },
      name: 'id'
    }, {
      data: function data(row) {
        return "".concat(row.duration, " minutes");
      },
      name: 'duration'
    }, {
      data: 'status_text',
      name: 'status'
    }, {
      data: 'password',
      name: 'password'
    }, {
      data: function data(row) {
        var btn = "<a href=\"javascript:void(0)\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-video-camera\"></i> Finished</a>";
        if (row.status == 1) {
          btn = "<a href=\"".concat(row.meta.join_url, "\" target=\"_blank\" class=\"btn btn-primary btn-sm\"><i class=\"fa fa-video-camera\"></i> Join Meeting</a>");
        }
        return btn;
      },
      name: 'duration',
      'searchable': false
    }],
    drawCallback: function drawCallback() {
      this.api().state.clear();
    },
    'fnInitComplete': function fnInitComplete() {
      $('#filter_user').change(function () {
        tbl.ajax.reload();
      });
    }
  });
});
/******/ 	return __webpack_exports__;
/******/ })()
;
});