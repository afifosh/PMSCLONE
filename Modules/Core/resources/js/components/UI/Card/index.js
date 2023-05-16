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
import ICardComponent from './ICard.vue'
import ICardHeadingComponent from './ICardHeading.vue'
import ICardBodyComponent from './ICardBody.vue'
import ICardFooterComponent from './ICardFooter.vue'

const ICardPlugin = {
  install(app) {
    app.component('ICard', ICardComponent)
    app.component('ICardHeading', ICardHeadingComponent)
    app.component('ICardBody', ICardBodyComponent)
    app.component('ICardFooter', ICardFooterComponent)
  },
}

// Components
export const ICard = ICardComponent
export const ICardHeading = ICardHeadingComponent
export const ICardBody = ICardBodyComponent
export const ICardFooter = ICardFooterComponent

// Plugin
export default ICardPlugin
