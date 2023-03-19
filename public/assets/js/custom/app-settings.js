document.addEventListener('DOMContentLoaded', function () {
    (function () {
        let provider = document.getElementById('provider')
        let providerDataAttr = provider.getAttribute('data-attr')

        let deliveryServices = Array.from(document.querySelectorAll('.deliveryService'))

        provider.addEventListener('change', e => {
            const selected = e.target.options[e.target.selectedIndex].value

            loadDeliverySettings(selected)

            hideDeliveryServices(deliveryServices)
            showDeliveryService(selected)
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

let loadDeliverySettings = (selectedProvider) => {
    const map = {
        amazon: 'ses',
        mailgun: 'mailgun',
        smtp: 'smtp',
        mailtrap: 'mailtrap',
        sendmail: 'sendmail',
    }

    window.axios.get(provider.getAttribute('data-tokens') + '?provider=' + selectedProvider).then(response => {
        const data = response.data
        const skipKeys = ['from_name', 'from_email', 'provider']

        Object.keys(data).forEach(key => {
            if (skipKeys.findIndex(findKey => findKey == key) !== -1) {
                return true
            }

            const id = map[selectedProvider] + '_' + key
            const element = document.getElementById(id)

            if (element.nodeName === 'INPUT')
                return element.value = data[key]

            if (element.nodeName === 'SELECT')
                $(element).selectpicker('val', data[key])
        })
    })
}