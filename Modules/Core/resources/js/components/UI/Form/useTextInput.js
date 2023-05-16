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
import { ref } from 'vue'
export function useTextInput(elRef, emit, currentValue) {
  const valueWhenFocus = ref(null)

  function blurHandler(e) {
    emit('blur', e)

    if (currentValue.value !== valueWhenFocus.value) {
      emit('change', currentValue.value)
    }
  }

  function focusHandler(e) {
    emit('focus', e)

    valueWhenFocus.value = currentValue.value
  }

  function keyupHandler(e) {
    emit('keyup', e)
  }

  function keydownHandler(e) {
    emit('keydown', e)
  }

  function blur() {
    elRef.value.blur()
  }

  function click() {
    elRef.value.click()
  }

  function focus(options) {
    elRef.value.focus(options)
  }

  function select() {
    elRef.value.select()
  }

  function setRangeText(replacement) {
    elRef.value.setRangeText(replacement)
  }

  return {
    setRangeText,
    select,
    focus,
    click,
    blur,
    keydownHandler,
    keyupHandler,
    blurHandler,
    focusHandler,
    valueWhenFocus,
  }
}
