/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */
export default {
  beforeMount: function (el, binding) {
    el._toggle = e => {
      const toggleElement = document.getElementById(binding.value)
      if (
        toggleElement.style.display === 'none' ||
        toggleElement.classList.contains('hidden')
      ) {
        toggleElement.style.display = 'block'
        toggleElement.classList.remove('hidden')
      } else {
        toggleElement.style.display = 'none'
      }
    }

    el.addEventListener('click', el._toggle)
  },
  unmounted: function (el, binding) {
    el.removeEventListener('click', el._toggle)
  },
}
