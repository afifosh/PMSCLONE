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
class WindowState {
  constructor() {
    this.hasSupport = 'history' in window && 'pushState' in history
  }
  /**
   * Push in history state
   *
   * @param  {string|null} url
   * @param  {Object|null|string} state
   * @param  {null|string} title
   *
   * @return {void}
   */
  push(url, state = {}, title = null) {
    if (!this.hasSupport) {
      return
    }

    window.history.pushState(state, title, url)
  }

  /**
   * Replace history state
   *
   * @param  {string|null} url
   * @param  {Object|null|string} state
   * @param  {null|string} title
   *
   * @return {void}
   */
  replace(url, state = null, title = null) {
    if (!this.hasSupport) {
      return
    }

    window.history.replaceState(state || window.history.state, title, url)
  }

  /**
   * Clear state hash
   *
   * @param  {String} replaceWith
   *
   * @return {Void}
   */
  clearHash(replaceWith = ' ') {
    return this.replace(replaceWith)
  }
}

export default new WindowState()
