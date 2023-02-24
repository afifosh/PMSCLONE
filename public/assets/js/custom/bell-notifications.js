$(document).ready(function () {
    getNotifications()
})

function getNotifications() {
    let url = $('#notifications-onload').attr('data-href')
    $.ajax({
        type: 'GET',
        url,
    }).done(function (response) {
        $('.dropdown-notifications-ul-list').append(response.data)
    })
}


$('.dropdown-notifications a.dropdown-toggle').on('click', function () {
    let url = $(this).attr('data-href')
    $.ajax({
        type: 'PUT',
        url,
    }).done(function () {
        $('.notification-bell').hide();
    })
})