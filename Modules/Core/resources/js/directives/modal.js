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
  beforeMount: function (el, binding, vnode) {
    el._showModal = () => {
      Innoclapps.$emit('modal-show', binding.value)
    }
    el.addEventListener('click', el._showModal)
  },
  unmounted: function (el, binding, vnode) {
    el.removeEventListener('click', el._showModal)
  },
}
