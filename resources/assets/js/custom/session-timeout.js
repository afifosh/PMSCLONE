$(document).ready(function () {
    $.sessionTimeout({
        keepAliveUrl,
        logoutUrl,
        redirUrl,
        warnAfter,
        redirAfter,
        countdownBar: true,
        countdownMessage: 'Redirecting in {timer} seconds.',
        useLocalStorageSynchronization: true,
        ignoreUserActivity: true,
        clearWarningOnUserActivity: false,
    });
})