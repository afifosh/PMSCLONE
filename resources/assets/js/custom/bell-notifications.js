window.getNotifications = function () {
    let url = $('#notifications-onload').attr('data-href')
    $.ajax({
        type: 'GET',
        url,
    }).done(function (response) {
        $('.dropdown-notifications-ul-list').empty().append(response.data)
    })
}

window.notificationSound = function() {
  let sound = document.getElementById("notificationSound");
  sound.currentTime = 0;
  sound.play();
}

$('.dropdown-notifications a.dropdown-toggle').on('click', function () {
    let url = $(this).attr('data-href')
    $.ajax({
        type: 'PUT',
        url,
    }).done(function () {
        $('.notification-bell').hide();
        $('.notification-bell').text(0);
    })
})
