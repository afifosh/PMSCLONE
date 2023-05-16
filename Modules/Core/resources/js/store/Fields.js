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
const state = {
  // Fields by groups for forms
  fields: {},
  placeholders: null,
  placeholdersResources: ['contacts', 'companies', 'deals'],
  placeholdersRequestInProgress: false,
}

const mutations = {
  /**
   * Set the given fields and group in store
   *
   * @param {Object} state
   * @param {Object} payload
   */
  SET(state, payload) {
    state.fields[payload.group] = payload.fields
  },

  /**
   * Set the given fields and group in store
   *
   * @param {Object} state
   */
  RESET(state) {
    state.fields = {}
    state.placeholders = null
  },

  /**
   * Set the application placeholders in store
   *
   * @param {Object} state
   * @param {Object} placeholders
   */
  SET_PLACEHOLDERS(state, placeholders) {
    state.placeholders = placeholders
  },

  /**
   * Set placeholders request state
   *
   * @param {Object} state
   * @param {Boolean} value
   */
  PLACEHOLDERS_REQUEST_IN_PROGRESS(state, value) {
    state.placeholdersRequestInProgress = value
  },
}

const actions = {
  /**
   * Get fields for given group/resource and view
   *
   * @param  {Function} options.commit
   * @param  {Object} options.state
   * @param  {String} options.group
   * @param  {String} options.view
   *
   * @return {Array}
   */
  async get({ commit, state }, { group, view }) {
    let storeGroup = group + '-' + view

    if (state.fields[storeGroup] !== undefined) {
      return state.fields[storeGroup]
    }

    let { data: fields } = await Innoclapps.request().get(
      '/fields/' + group + '/' + view,
      { params: { intent: view } }
    )

    commit('SET', { group: storeGroup, fields: fields })

    return fields
  },

  /**
   * Get fields for given group/resource and view
   *
   * @param  {Function} options.commit
   * @param  {Object} options.state
   * @param  {String} options.resourceName
   * @param  {String} options.view
   * @param  {Number} options.resourceId
   *
   * @return {Array}
   */
  async getForResource(
    { commit, state },
    { resourceName, view, resourceId, viaResource, viaResourceId }
  ) {
    let storeGroup =
      resourceName + '-' + view + (viaResource ? '-' + viaResource : '')

    // We don't cache the fields when resourceId/update fields are requested
    // Because the resource may implement different strategies based on the model
    // e.q. readonly if specific model condition is met
    if (state.fields[storeGroup] !== undefined && !resourceId) {
      return state.fields[storeGroup]
    }

    let { data: fields } = await Innoclapps.request().get(
      `/${resourceName}${resourceId ? '/' + resourceId : ''}/${view}-fields`,
      {
        params: {
          intent: view,
          via_resource: viaResource,
          via_resource_id: viaResourceId,
        },
      }
    )

    commit('SET', { group: storeGroup, fields: fields })

    return fields
  },

  /**
   * Retrieve application wide placeholders
   *
   * @param  {Function} options.commit
   * @param  {Object} options.state
   *
   * @return {Promise}
   */
  async fetchPlaceholders({ commit, state }) {
    if (state.placeholders !== null || state.placeholdersRequestInProgress) {
      return
    }

    commit('PLACEHOLDERS_REQUEST_IN_PROGRESS', true)

    await Innoclapps.request()
      .get('/placeholders', {
        params: {
          resources: state.placeholdersResources,
        },
      })
      .then(({ data }) => {
        let groups = Object.keys(data)

        groups.forEach(group => {
          data[group].placeholders = data[group].placeholders.map(
            placeholder => {
              // We don't need the interpolation data for the front-end
              delete placeholder.interpolation_start
              delete placeholder.interpolation_end

              return placeholder
            }
          )
        })

        commit('SET_PLACEHOLDERS', data)
      })
      .finally(() => commit('PLACEHOLDERS_REQUEST_IN_PROGRESS', false))
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
}
