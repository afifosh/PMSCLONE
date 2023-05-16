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
import IConfirmationDialogComponent from './IConfirmationDialog.vue'
import IModalComponent from './IModal.vue'
import ISlideoverComponent from './ISlideover.vue'

const IDialogPlugin = {
  install(app, options = {}) {
    app.component('IConfirmationDialog', IConfirmationDialogComponent)
    app.component('IModal', IModalComponent)
    app.component('ISlideover', ISlideoverComponent)

    app.config.globalProperties.$iModal = {
      hide(id) {
        options.globalEmitter.$emit('modal-hide', id)
      },
      show(id) {
        options.globalEmitter.$emit('modal-show', id)
      },
    }
  },
}

// Components
export const IConfirmationDialog = IConfirmationDialogComponent
export const IModal = IModalComponent
export const ISlideover = ISlideoverComponent

// Plugin
export default IDialogPlugin
