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
  viaResource: String,
  viaResourceId: Number,
  field: { type: Object, required: true },
  isFloating: { type: Boolean, default: false },
  formId: { type: String, required: true },

  view: {
    required: true,
    type: String,
    validator: function (value) {
      return (
        [
          ...Object.keys(Innoclapps.config('fields.views')),
          ...['internal'],
        ].indexOf(value) !== -1
      )
    },
  },
}
