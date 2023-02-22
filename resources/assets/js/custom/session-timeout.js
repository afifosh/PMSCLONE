$(document).ready(function () {
    const SECOND_CONVERSION = 1000;
    $.sessionTimeout({
        keepAliveUrl,
        logoutUrl,
        redirUrl,
        warnAfter: warnAfter * SECOND_CONVERSION,
        redirAfter: redirAfter * SECOND_CONVERSION,
        countdownBar: true,
        countdownMessage: 'Redirecting in {timer} seconds.',
        useLocalStorageSynchronization: true,
        ignoreUserActivity: true,
        clearWarningOnUserActivity: false,
    });
})