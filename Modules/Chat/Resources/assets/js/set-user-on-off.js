window.setLastSeenOfUser = function (status) {
    $.ajax({
        type: 'post',
        url: route('update-last-seen'),
        data: { status: status },
        success: function (data) {
        },
    });
};

//set user status online
setLastSeenOfUser(1);

window.onbeforeunload = function () {
    Echo.leave('user-status');
    setLastSeenOfUser(0);
    //return undefined; to prevent dialog while window.onbeforeunload
    return undefined;
};

Echo.join(`user-status`).here((users) => {
  setTimeout(function () {
      $.each(users, function (index, user) {
          update_avatars(user, 1);
      });
  }, 1000);
}).joining((user) => {
  update_avatars(user, 1);
}).leaving((user) => {
  update_avatars(user, 0);
});

function update_avatars(user, status)
{
  let userAvatar = $('.u-avatar-' + user.id);
  if (userAvatar.length > 0) {
      if (status == 1) {
          userAvatar.removeClass('avatar-offline');
          userAvatar.addClass('avatar-online');
      } else {
          userAvatar.removeClass('avatar-online');
          userAvatar.addClass('avatar-offline');
      }
  }
}

