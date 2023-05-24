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
import { createStore } from 'vuex'
import findIndex from 'lodash/findIndex'

export default createStore({
  state: {
    pageTitle: '',
    apiURL: null,
    url: null,
    menu: [],
    timezones: [],
    sidebarOpen: false,
    settings: {},
  },
  mutations: {
    /**
     * Set the application settings in store
     *
     * @param {Object} state
     * @param {Object} settings
     */
    SET_SETTINGS(state, settings) {
      Object.keys(settings).forEach((settingKey, index) => {
        state.settings[settingKey] = settings[settingKey]
      })
    },

    /**
     * Set application available timezones
     */
    SET_TIMEZONES(state, timezones) {
      state.timezones = timezones
    },

    /**
     * Set page title
     * Somehow Vue-Router does not make the meta reactive, we need to use the store
     */
    SET_PAGE_TITLE(state, title) {
      state.pageTitle = title
      document.title = title
    },

    /**
     * Toggle the sidebar visibility
     */
    SET_SIDEBAR_OPEN(state, value) {
      state.sidebarOpen = value
    },

    /**
     * Set available menu items
     */
    SET_MENU(state, menu) {
      state.menu = menu
    },

    /**
     * Update menu item
     */
    UPDATE_MENU_ITEM(state, payload) {
      const index = findIndex(state.menu, ['id', payload.id])
      state.menu[index] = Object.assign({}, state.menu[index], payload.data)
    },

    /**
     * Set application url
     */
    SET_URL(state, url) {
      state.url = url
    },

    /**
     * Set application HTTP api url
     */
    SET_API_URL(state, apiURL) {
      state.apiURL = apiURL
    },
  },
  getters: {
    /**
     * Get the application available locales
     *
     * @param  {Object} state
     *
     * @return {Array}
     */
    locales(state) {
      return Innoclapps.config('locales')
    },

    /**
     * Get single menu item by given id
     */
    getMenuItem: state => id => {
      return state.menu[findIndex(state.menu, ['id', id])]
    },
  },
  actions: {
    /**
     * Logout the logged in user
     */
    logout({ state }) {
      Innoclapps.request()
        .post(state.url + '/logout')
        .then(() => {
          window.location.href = Innoclapps.config('url') + '/login'
        })
    },

    /**
     * Fetch the available timezones from storage
     */
    fetchTimezones({ commit }) {
      Innoclapps.request()
        .get('/timezones')
        .then(({ data }) => {
          Object.freeze(data)
          commit('SET_TIMEZONES', data)
        })
    },
  },
  modules: {},
  strict: process.env.NODE_ENV !== 'production',
})
