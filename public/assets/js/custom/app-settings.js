document.addEventListener('DOMContentLoaded', function () {
    (function () {
        let provider = document.getElementById('provider')
        let providerDataAttr = provider.getAttribute('data-attr')

        let deliveryServices = Array.from(document.querySelectorAll('.deliveryService'))

        provider.addEventListener('change', e => {
            hideDeliveryServices(deliveryServices)
            showDeliveryService(e.target.options[e.target.selectedIndex].value)
        })

        if (providerDataAttr != '') {
            hideDeliveryServices(deliveryServices)
            showDeliveryService(providerDataAttr)
        }
    })();
})

let hideDeliveryServices = (deliveryServices) => {
    deliveryServices.forEach(deliveryService => {
        deliveryService.classList.add('d-none')
    })
}

let showDeliveryService = (deliveryService) => {
    const classSuffix = 'Service'

    document.getElementById(deliveryService + classSuffix).classList.remove('d-none')
}