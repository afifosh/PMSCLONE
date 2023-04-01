<button type="button" {{ $attributes->merge([
    'class' => 'd-inline-flex btn',
    'id'    => ''
    ])
}} onclick="elementsToBeCleared(this)">
    {{ $slot }}
</button>

<script>
    function elementsToBeCleared(e) {
        const elements = e.closest('form').querySelectorAll('input, select, file')
        const clearOutsideElements = document.querySelectorAll('.clear-with-form')

        clearForm(elements)
        clearForm(clearOutsideElements)
    }

    function clearForm(elements) {
        elements.forEach(element => {
            if (element.getAttribute('type') === 'hidden' || element.classList.contains('do-not-clear-with-form')) {
                return true
            }

            if(element.nodeName === 'IMG') {
                element.src = ''
                return true
            }

            if (element.nodeName === 'SELECT' && 'selectpicker' in $(element)) {
                $(element).selectpicker('val', '')
                return true
            }

            if (element.getAttribute('type') === 'file') {
                let parentForm = element.closest('form')

                let newInput = document.createElement('input')
                newInput.setAttribute('name', element.name + '-file-deleted');
                newInput.setAttribute('type', 'hidden');

                parentForm.append(newInput)
                return true
            }
            
            // else clear the value
            element.value = ''
        })
    }
</script>