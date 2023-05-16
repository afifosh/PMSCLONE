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
const VALID_PLACEMENTS = [
  'top',
  'top-start',
  'top-end',
  'right',
  'right-start',
  'right-end',
  'bottom',
  'bottom-start',
  'bottom-end',
  'left',
  'left-start',
  'left-end',
]

const VALID_VARIANTS = ['dark', 'light']

function findModifier(availableModifiers, modifiers) {
  return availableModifiers.reduce((acc, cur) => {
    if (modifiers[cur]) acc = cur
    return acc
  }, '')
}

const updateAttributes = (el, binding) => {
  const { modifiers, value } = binding

  if (!value) return

  const placement = findModifier(VALID_PLACEMENTS, modifiers) || 'top'
  const variant = findModifier(VALID_VARIANTS, modifiers) || 'dark'

  el.setAttribute('v-placement', placement)
  el.setAttribute('v-tooltip', value)
  el.setAttribute('v-variant', variant)
}

export default {
  beforeMount: (el, binding) => updateAttributes(el, binding),
  updated: (el, binding) => updateAttributes(el, binding),
  beforeUnmount(el) {
    el.removeAttribute('v-tooltip')
    el.removeAttribute('v-placement')
    el.removeAttribute('v-variant')
  },
}
