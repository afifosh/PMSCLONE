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
import ResourceCrud from '@/store/actions/ResourceCrud'

export default {
  /**
   * Fetch records from storage
   *
   * @param  {Object} context
   * @param  {Object} options
   *
   * @return {Array}
   */
  async fetch(context, options = {}) {
    if (context.state.dataFetched && !options.force) {
      return context.state.collection
    }

    let records = await ResourceCrud.fetch(context, options)

    context.commit('SET', records)

    return records
  },

  /**
   * Get single record from database
   *
   * @param  {Object} context
   * @param  {Number|Object} options
   *
   * @return {Object}
   */
  async get(context, options) {
    return ResourceCrud.get(context, options)
  },

  /**
   * Store a record
   *
   * @param  {Object} context
   * @param  {Object} form
   *
   * @return {Object}
   */
  async store(context, form) {
    let record = await ResourceCrud.store(context, form)

    context.commit('ADD', record)

    return record
  },

  /**
   * Update a record
   *
   * @param  {Object} context
   * @param  {Object} payload
   *
   * @return {Object}
   */
  async update(context, payload) {
    let record = await ResourceCrud.update(context, payload)

    context.commit('UPDATE', { id: payload.id, item: record })

    return record
  },

  /**
   * Delete a record
   *
   * @param  {Object} context
   * @param  {Number} id
   *
   * @return {Boolean}
   */
  async destroy(context, id) {
    let result = await ResourceCrud.destroy(context, id)

    context.commit('REMOVE', id)

    return result
  },
}
