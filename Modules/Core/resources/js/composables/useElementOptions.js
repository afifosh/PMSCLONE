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

export function useElementOptions() {
  const options = ref([])

  /**
   * Set the options
   *
   * @param {Array} list
   * @param {Function} callback
   */
  function setOptions(list, callback) {
    options.value = list

    if (callback) {
      callback(options)
    }
  }

  /**
   * Get option from object that may hold options or options settings
   *
   * @param  {Object|Array} options
   *
   * @return {Promise}
   */
  async function getOptions(options) {
    if (Array.isArray(options)) {
      return options
    }

    return options.options
  }

  return { options, setOptions, getOptions }
}
