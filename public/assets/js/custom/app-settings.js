document.addEventListener('DOMContentLoaded', function () {
    (function () {
        const setting = document.querySelector('.setting'),
            settingList = [].slice.call(document.querySelectorAll('.setting-list li')),
            settingSidebar = document.querySelector('.setting-sidebar'),
            appOverlay = document.querySelector('.app-overlay'),
            settingItems = [].slice.call(document.querySelectorAll('.setting-item'));

        if (setting) {
            new PerfectScrollbar(setting, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        settingList.forEach(settingListItem => {
            settingListItem.addEventListener('click', e => {
                let currentTarget = e.currentTarget,
                    currentTargetData = currentTarget.getAttribute('data-target');

                settingSidebar.classList.remove('show');
                appOverlay.classList.remove('show');

                // Remove active class from each folder filters
                Helpers._removeClass('active', settingList);
                // Add active class to selected folder filters
                currentTarget.classList.add('active');
                settingItems.forEach(settingItem => {
                    // If folder filter is Inbox
                    if (currentTargetData == 'inbox') {
                        settingItem.classList.add('d-block');
                        settingItem.classList.remove('d-none');
                    } else if (settingItem.hasAttribute('data-' + currentTargetData)) {
                        settingItem.classList.add('d-block');
                        settingItem.classList.remove('d-none');
                    } else {
                        settingItem.classList.add('d-none');
                        settingItem.classList.remove('d-block');
                    }
                });
            });
        });

        $('#supportedMailServices').on('change', function () {
            let selectedEmailService = $(this).val() + 'EmailService'
            $('.email-service').addClass('d-none')
            $('#' + selectedEmailService).removeClass('d-none')
        })
    })();
})