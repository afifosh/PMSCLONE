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
import PersistentResourceCrud from '@/store/actions/PersistentResourceCrud'
import PersistentResourceMutations from '@/store/mutations/PersistentResourceMutations'
import PersistentResourceGetters from '@/store/getters/PersistentResourceGetters'
import orderBy from 'lodash/orderBy'
import find from 'lodash/find'
import filter from 'lodash/filter'

const state = {
  collection: [],
  dataFetched: false,
  endpoint: '/mail/accounts',
  syncInProgress: false,
  accountConfigError: null,
  formConnectionState: false,
  activeInboxAccount: {},
}

const mutations = {
  ...PersistentResourceMutations,

  /**
   * Sets that there is a configuration error an account
   *
   * @param {Object} state
   * @param {mixed} error
   */
  SET_ACCOUNT_CONFIG_ERROR(state, error) {
    state.accountConfigError = error
  },

  /**
   * Set that indicator that synchronization is in progress
   *
   * @param  {Object} state
   * @param  {Boolean} bool
   *
   * @return {void}
   */
  SET_SYNC_IN_PROGRESS(state, bool) {
    state.syncInProgress = bool
  },

  /**
   * Set the active inbox account
   *
   * @param  {Object} state
   * @param  {Object|Number} account
   *
   * @return {void}
   */
  SET_INBOX_ACCOUNT(state, account) {
    if (typeof account != 'number') {
      account = account.id
    }

    state.activeInboxAccount = account
  },

  /**
   * Set the account connection state for the form
   *
   * @param {Object} state
   * @param {Boolean} bool
   */
  SET_FORM_CONNECTION_STATE(state, bool) {
    state.formConnectionState = bool
  },

  /**
   * Set the given account id as primary
   * The function unsets any previous primary accounts from the store
   * and updates the given account id to be as primary
   *
   * @param {Object} state
   * @param {Number} id|null When passing null, all accounts are marked as not primary
   */
  SET_ACCOUNT_AS_PRIMARY(state, id) {
    // Update previous is_primary to false and set passed id as primary
    // this helps the getter "accounts" to properly perform the sorting
    state.collection.forEach((account, index) => {
      state.collection[index].is_primary = account.id == id
    })
  },
}

const getters = {
  ...PersistentResourceGetters,

  /**
   * Get the shared accounts the user is able to view
   *
   * @param  {Object} state
   *
   * @return {Array}
   */
  shared(state) {
    return filter(state.collection, ['type', 'shared'])
  },

  /**
   * Get the user personal accounts
   *
   * @param  {Object} state
   *
   * @return {Array}
   */
  personal(state) {
    return filter(state.collection, ['type', 'personal'])
  },

  /**
   * Get account OAuth Connect URL
   *
   * @param  {Object} state)
   *
   * @return {String}
   */
  OAuthConnectUrl: state => (connection_type, type) => {
    if (connection_type == 'Gmail') {
      return (
        Innoclapps.config('url') + '/mail/accounts/' + type + '/google/connect'
      )
    } else if (connection_type == 'Outlook') {
      return (
        Innoclapps.config('url') +
        '/mail/accounts/' +
        type +
        '/microsoft/connect'
      )
    }
  },

  /**
   * Get the active inbox acccount
   *
   * @param  {Object} state
   *
   * @return {Object}
   */
  activeInboxAccount(state) {
    return (
      find(state.collection, ['id', Number(state.activeInboxAccount)]) || {}
    )
  },

  /**
   * Get all accounts sorted by first primary acccounts
   * then by email
   *
   * @param  {Object} state
   *
   * @return {Array}
   */
  accounts(state) {
    return orderBy(state.collection, ['is_primary', 'email'], ['desc', 'asc'])
  },

  /**
   * Check whether there are accounts configured for the current user
   *
   * @param  {Object} state
   *
   * @return {Boolean}
   */
  hasConfigured(state) {
    return state.collection.length > 0
  },

  /**
   * Get the latest created account
   *
   * @param  {Object} state
   *
   * @return {Object}
   */
  latest(state) {
    return orderBy(state.collection, account => new Date(account.created_at), [
      'desc',
    ])[0]
  },
}

const actions = {
  ...PersistentResourceCrud,

  /**
   * Remove primary account
   *
   * @param {Object} options.state
   * @param {Function} options.commit
   *
   * @return {Void}
   */
  removePrimary({ state, commit }) {
    Innoclapps.request()
      .delete(`${state.endpoint}/primary`)
      .then(() => {
        commit('SET_ACCOUNT_AS_PRIMARY', null)
      })
  },

  /**
   * Set the account is primary state
   *
   * @param {Object} options.state
   * @param {Function} options.commit
   * @param {Object} payload
   *
   * @return {Void}
   */
  setPrimary({ state, commit }, id) {
    Innoclapps.request()
      .put(`${state.endpoint}/${id}/primary`)
      .then(() => {
        commit('SET_ACCOUNT_AS_PRIMARY', id)
      })
  },

  /**
   * Enable account synchronization
   *
   * @param {Object} options.state
   * @param {Function} options.commit
   * @param {Int} id
   *
   * @return {Void}
   */
  enableSync({ state, commit }, id) {
    Innoclapps.request()
      .post(`${state.endpoint}/${id}/sync/enable`)
      .then(({ data: account }) => {
        commit('UPDATE', {
          id: account.id,
          item: account,
        })
      })
  },

  /**
   * Disable account synchronization
   *
   * @param {Object} options.state
   * @param {Function} options.commit
   * @param {Int} id
   *
   * @return {Void}
   */
  disableSync({ state, commit }, id) {
    Innoclapps.request()
      .post(`${state.endpoint}/${id}/sync/disable`)
      .then(({ data: account }) => {
        commit('UPDATE', {
          id: account.id,
          item: account,
        })
      })
  },

  /**
   * Syncs shared email account
   *
   * @param  {Function} options.commit
   * @param  {Number} accountId
   *
   * @return {Object}
   */
  async syncAccount({ commit }, accountId) {
    commit('SET_SYNC_IN_PROGRESS', true)

    let { data } = await Innoclapps.request()
      .get(`/mail/accounts/${accountId}/sync`)
      .finally(() => commit('SET_SYNC_IN_PROGRESS', false))

    return data
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
    let { data } = await Innoclapps.request().delete(`${state.endpoint}/${id}`)

    context.commit('REMOVE', id)
    context.dispatch('updateUnreadCountUI', data.unread_count)

    return data
  },

  /**
   * Update the total unread count UI
   *
   * @param  {Object} context
   * @param  {Number} unreadCount
   *
   * @return {Void}
   */
  updateUnreadCountUI(context, unreadCount) {
    context.commit(
      'UPDATE_MENU_ITEM',
      {
        id: 'inbox',
        data: {
          badge: unreadCount,
        },
      },
      { root: true }
    )
  },

  /**
   * Decrement total unread count UI
   *
   * @param  {Object} context
   *
   * @return {Void}
   */
  decrementUnreadCountUI(context) {
    let item = context.rootGetters.getMenuItem('inbox')

    if (item.badge < 1) {
      return
    }

    context.dispatch('updateUnreadCountUI', item.badge - 1)
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  getters,
  actions,
}
