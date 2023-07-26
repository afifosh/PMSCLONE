/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/notification.js ***!
  \**********************************************************/
var totalNotification = 0;
var mediaTypeImage = 1;
var mediaTypePdf = 2;
var mediaTypeDocx = 3;
var mediaTypeVoice = 4;
var mediaTypeVideo = 5;
var mediaTypeTxt = 7;
var mediaTypeXls = 8;
window.getNotificationMsgTime = function (dateTime) {
  var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'h:mma';
  return moment.utc(dateTime).local().format(format);
};
window.hideNoNotification = function () {
  $('#noNewNotification').hide();
};
window.showNoNotification = function () {
  $('#noNewNotification').show();
};
window.setTotalNotificationCount = function (totalNotification) {
  $('.totalNotificationCount').text(totalNotification).attr('data-total_count', totalNotification).show();
  if (totalNotification > 0) {
    $('.read-all-notification').show();
    $('.totalNotificationCount').show();
    hideNoNotification();
  } else {
    $('.read-all-notification').hide();
    $('.totalNotificationCount').hide();
    showNoNotification();
  }
};
window.getMessageByItsTypeForNotificationList = function (message, message_type) {
  var file_name = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  if (message_type === mediaTypeImage) {
    return '<i class="fa fa-camera mx-0 text-left" aria-hidden="true"></i>' + ' Photo';
  } else if (message_type === mediaTypePdf) {
    return '<i class="fa fa-file-pdf-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else if (message_type === mediaTypeDocx) {
    return '<i class="fa fa-file-word-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else if (message_type === mediaTypeVoice) {
    return '<i class="fa fa-file-audio-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else if (message_type === mediaTypeVideo) {
    return '<i class="fa fa-file-video-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else if (message_type === mediaTypeTxt) {
    return '<i class="fa fa-file-text-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else if (message_type === mediaTypeXls) {
    return '<i class="fa fa-file-excel-o mx-0 text-left" aria-hidden="true"></i>' + ' ' + file_name;
  } else {
    return message;
  }
};
window.renderNotifications = function () {
  var template = $.templates('#tmplNotification');
  var myHelpers = {
    getNotificationMsgTime: getNotificationMsgTime,
    getMessageByItsTypeForNotificationList: getMessageByItsTypeForNotificationList
  };
  var notificationHtml = template.render(notifications, myHelpers);
  totalNotification = notifications.length;
  setTotalNotificationCount(totalNotification);
  $('.notification__popup').append(notificationHtml);
};
renderNotifications();
window.Echo["private"]("user.".concat(loggedInUserId)).listen('UserEvent', function (notification) {
  if (notification.type !== 6) {
    // message deleted for everyone
    return false;
  }
  var currentUrl = window.location.href;
  if (currentUrl.indexOf(conversationsURL) != -1 && getCurrentUserId() === notification.owner_id) {
    return false;
  }
  hideNoNotification();
  var isUserElePresent = $('.notification__popup').find('#owner-' + notification.owner_id).length;
  var myHelpers = {
    getNotificationMsgTime: getNotificationMsgTime,
    getMessageByItsTypeForNotificationList: getMessageByItsTypeForNotificationList
  };
  if (isUserElePresent) {
    var template = $.templates('#tmplOldNotification');
    var notificationHtml = template.render(notification, myHelpers);
    $('#owner-' + notification.owner_id).html(notificationHtml);
  } else {
    totalNotification += 1;
    setTotalNotificationCount(totalNotification);
    var _template = $.templates('#tmplNotification');
    var _notificationHtml = _template.render(notification, myHelpers);
    $('.notification__popup').append(_notificationHtml);
  }
  notificationSound();
});
$(document).on('click', '.notification-item', function (e) {
  e.preventDefault();
  var notificationId = $(this).data('notification_id');
  var selector = $(this).attr('id');
  readNotification(notificationId, '#' + selector);
});
window.readNotification = function (notificationId, selector) {
  $.ajax({
    type: 'get',
    url: route('notification.read-notification', notificationId),
    success: function success(data) {
      totalNotification = totalNotification - 1;
      setTotalNotificationCount(totalNotification);
      var ownerId = data.data.owner_id;
      $(selector).remove();
      displayToastr('Success', 'success', data.message);
      var currentUrl = window.location.href;
      if (currentUrl.indexOf(conversationsURL) != -1) {
        var conversationEle = $(document).find('#user-' + ownerId);
        conversationEle.trigger('click');
      } else {
        window.location.replace(conversationsURL + '?conversationId=' + ownerId);
      }
    },
    error: function error(result) {
      displayToastr('Error', 'error', result.responseJSON.message);
    }
  });
};
window.readNotificationWhenChatWindowOpen = function (notificationId, selector) {
  $.ajax({
    type: 'get',
    url: route('notification.read-notification', notificationId),
    success: function success() {
      $(selector).remove();
      var notifications = $('.notification__popup').find('.notification-item').length;
      totalNotification = notifications;
      setTotalNotificationCount(totalNotification);
    },
    error: function error(result) {
      displayToastr('Error', 'error', result.responseJSON.message);
    }
  });
};
window.getCurrentUserId = function () {
  return $('.chat__person-box--active').data('id');
};
window.readNotificationWhenOpenChatWindow = function (senderId) {
  $('#owner-' + senderId).remove();
  var notifications = $('.notification__popup').find('.notification-item').length;
  totalNotification = notifications;
  setTotalNotificationCount(totalNotification);
};
$(document).on('click', '.read-all-notification', function (e) {
  e.preventDefault();
  if (!totalNotification > 0) {
    return false;
  }
  readAllNotification();
});
window.readAllNotification = function (notificationId, selector) {
  $.ajax({
    type: 'get',
    url: route('read-all-notification', notificationId),
    success: function success(data) {
      var senderIds = data.data.sender_ids;
      $.each(senderIds, function (index, val) {
        $("#user-" + val).find('.chat__person-box-count').text(0).hide();
      });
      totalNotification = 0;
      setTotalNotificationCount(totalNotification);
      $('.notification__popup').find('.notification-item').remove();
      displayToastr('Success', 'success', data.message);
    },
    error: function error(result) {
      displayToastr('Error', 'error', result.responseJSON.message);
    }
  });
};
/******/ })()
;