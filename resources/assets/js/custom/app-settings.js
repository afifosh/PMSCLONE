document.addEventListener('DOMContentLoaded', function () {
    (function () {
        // boot app settings
        bootAppSetting()
        // boot general settings
        bootGeneralSetting()
    })();
})

let bootAppSetting = () => {
    let provider = document.getElementById('provider')
    if (!provider) {
        return
    }

    let providerDataAttr = provider.getAttribute('data-attr')
    let deliveryServices = Array.from(document.querySelectorAll('.deliveryService'))

    provider.addEventListener('change', e => {
        const selected = e.target.options[e.target.selectedIndex].value
        loadDeliverySettings(selected, provider)
        hideDeliveryServices(deliveryServices)
        showDeliveryService(selected)
    })

    if (providerDataAttr != '') {
        hideDeliveryServices(deliveryServices)
        showDeliveryService(providerDataAttr)
    }
}

let hideDeliveryServices = (deliveryServices) => {
    deliveryServices.forEach(deliveryService => {
        deliveryService.classList.add('d-none')
    })
}

let showDeliveryService = (deliveryService) => {
    const classSuffix = 'Service'
    document.getElementById(deliveryService + classSuffix).classList.remove('d-none')
}

let loadDeliverySettings = (selectedProvider, provider) => {
    const map = {
        amazon: 'ses',
        mailgun: 'mailgun',
        smtp: 'smtp',
        mailtrap: 'mailtrap',
        sendmail: 'sendmail',
    }

    getDeliverySetting(map, selectedProvider, provider)
}

let getDeliverySetting = (map, selectedProvider, provider) => {
    window.axios.get(provider.getAttribute('data-tokens') + '?provider=' + selectedProvider).then(response => {
        const data = response.data
        const skipKeys = ['from_name', 'from_email', 'provider']

        Object.keys(data).forEach(key => {
            if (skipKeys.findIndex(findKey => findKey == key) !== -1) {
                return true
            }

            updateElement(`${map[selectedProvider]}_${key}`, data[key])
        })
    })
}

let updateElement = (id, newValue) => {
    const element = document.getElementById(id)

    if (element.nodeName === 'INPUT') {
        element.value = newValue
    }

    if (element.nodeName === 'SELECT') {
        $(element).selectpicker('val', newValue)
    }
}

// general settings below
let bootGeneralSetting = () => {
    const form = document.getElementById('general-setting-form')
    
    if(! form) {
        return
    }

    registerFileInputListeners(form.querySelectorAll('.img-holder-placeholder'))
}

let registerFileInputListeners = (inputs) => {
    inputs.forEach(input => input.addEventListener('click', onFileInputClick))
}

let onFileInputClick = (e) => {
    const imgHolder = e.target.closest('.img-holder')

    const img = imgHolder.getElementsByTagName('img')
    
    const fileInput = imgHolder.nextElementSibling

    // if the file input / img is not found return from function
    if (fileInput.type != 'file' || ! img || 0 in img == false) {
        return
    }

    // add event change listener as well
    fileInput.addEventListener('change', (e) => previewUploadedImage(e, img))

    // click the file
    fileInput.click()
}

let previewUploadedImage = (e, img) => {
    let selectedFile = e.target.files[0];
    var reader = new FileReader();
    reader.onload = function (event) {
        // Get the data URL of the file contents
        var dataUrl = event.target.result;
        // get the the image tag here
        img[0].src = dataUrl;
    };

    // Read the file as a data URL
    reader.readAsDataURL(selectedFile);
}