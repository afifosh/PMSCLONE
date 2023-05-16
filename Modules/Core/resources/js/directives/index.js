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
import Modal from './modal'
import Toggle from './toggle'

function registerDirectives(app) {
  app.directive('i-modal', Modal)
  app.directive('i-toggle', Toggle)
}

export default registerDirectives
export { Modal, Toggle }
