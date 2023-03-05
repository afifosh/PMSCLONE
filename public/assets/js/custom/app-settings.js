document.addEventListener('DOMContentLoaded', function () {
    (function () {
        $('#supportedMailServices').on('change', function () {
            let selectedEmailService = $(this).val() + 'EmailService'
            $('.email-service').addClass('d-none')
            $('#' + selectedEmailService).removeClass('d-none')
        })
    })();
})