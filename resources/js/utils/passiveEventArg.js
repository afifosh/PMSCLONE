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
/**
 * Get the passive third arguemtn and check whether the browser supports it
 *
 * @return {Boolen|Object}
 */
function passiveEventArg() {
  // Cache checks
  if (window.hasOwnProperty('__passiveEvt')) {
    return window.__passiveEvt
  }

  let result = false

  try {
    const arg = Object.defineProperty({}, 'passive', {
      get() {
        result = {
          passive: true,
        }
        return true
      },
    })

    window.addEventListener('testpassive', arg, arg)
    window.remove('testpassive', arg, arg)
  } catch (e) {
    /* */
  }

  window.__passiveEvt = result

  return result
}

export default passiveEventArg
