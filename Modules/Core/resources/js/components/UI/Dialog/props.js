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
import { randomString } from '@/utils'

export default {
  form: { type: Boolean },
  overlay: { type: Boolean, default: true },
  visible: { type: Boolean, default: false }, // v-model
  title: String,
  description: String,
  busy: Boolean,
  size: {
    type: String,
    default: 'md',
    validator(value) {
      return ['sm', 'md', 'lg', 'xl', 'xxl'].includes(value)
    },
  },
  id: { type: String, default: randomString() },

  okTitle: { type: String, default: 'Ok' },
  okDisabled: { type: Boolean, default: false },
  okLoading: { type: Boolean, default: false },
  okVariant: { type: String, default: 'primary' },
  okSize: String,

  cancelTitle: { type: String, default: 'Cancel' },
  cancelVariant: { type: String, default: 'white' },
  cancelDisabled: { type: Boolean, default: false },
  cancelSize: String,

  hideFooter: Boolean,
  hideHeader: Boolean,
  hideHeaderClose: Boolean,
  initialFocus: { type: Object, default: null },
  staticBackdrop: Boolean, // prevent dialog close on esc and backdrop click
}
