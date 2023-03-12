document.addEventListener('DOMContentLoaded', function () {
    (function () {
        let provider = document.getElementById('provider')

        let deliveryServices = Array.from(document.querySelectorAll('.deliveryService'))

        provider.addEventListener('change', e => {
            deliveryServices.forEach(deliveryService => {
                deliveryService.classList.add('d-none')
            })

            const classSuffix = 'Service'

            document.getElementById(
                e.target.options[e.target.selectedIndex].value + classSuffix
            ).classList.remove('d-none')
        })
    })();
})