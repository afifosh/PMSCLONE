// import { createApp } from 'vue';
// import ExampleComponent from './components/ExampleComponent.vue';

// const app = createApp({});
// app.component('example-component', ExampleComponent);
// app.mount('#app');




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
// import 'unfonts.css'
// import '../css/app.css'
import get from 'lodash/get'
import { createApp } from 'vue'
import registerComponents from '~/Core/resources/js/components'
import registerFields from '~/Core/resources/js/fields'
import registerDirectives from '~/Core/resources/js/directives'
import Broadcast from '~/Core/resources/js/services/Broadcast'
import VoIP from '~/Core/resources/js/services/VoIP'
import i18n from '~/Core/resources/js/i18n'
import store from '@/store'
import HTTP from '~/Core/resources/js/services/HTTP'
import mitt from 'mitt'
import router from '@/router'
import Mousetrap from 'mousetrap'

import '~/Core/resources/js/element-prototypes'
import '~/Core/resources/js/plugins'

import '~/Core/resources/js/app.js'

// import '~/Activities/resources/js/app.js'
// import '~/Billable/resources/js/app.js'
// import '~/Brands/resources/js/app.js'
// import '~/Calls/resources/js/app.js'
// import '~/Comments/resources/js/app.js'
// import '~/Contacts/resources/js/app.js'
import '~/Core/resources/js/app.js'
// import '~/Deals/resources/js/app.js'
// import '~/Documents/resources/js/app.js'
import '~/MailClient/resources/js/app.js'
// import '~/Notes/resources/js/app.js'
// import '~/Translator/resources/js/app.js'
// import '~/Users/resources/js/app.js'
// import '~/WebForms/resources/js/app.js'
// import '~/ThemeStyle/resources/js/app.js'

window.CreateApplication = (config, callbacks = []) =>
  new Application(config).booting(callbacks)

export default class Application {
  constructor(config) {
    this.bus = mitt()
    this.appConfig = config
    this.bootingCallbacks = []
    this.axios = HTTP

    this.axios.defaults.baseURL = config.apiURL
  }

  /**
   * Start the application
   *
   * @return {Void}
   */
  start() {
    let self = this

    const data = {
      confirmationDialog: null,
    }

    Mousetrap.init()

    const app = createApp({
      data() {
        return data
      },
      mounted() {
        self.$on('conflict', message => {
          if (message) {
            self.info(message)
          }
        })

        self.$on('error-404', () => {
          router.replace({
            name: '404',
          })
        })

        self.$on('error-403', error => {
          if (error.response.config.url !== '/broadcasting/auth') {
            router.replace({
              name: '403',
              query: { message: error.response.data.message },
            })
          }
        })

        self.$on('error', message => {
          if (message) {
            self.error(message)
          }
        })

        self.$on('too-many-requests', () => {
          self.error(this.$t('core::app.throttle_error'))
        })

        self.$on('token-expired', () => {
          self.error(
            this.$t('core::app.token_expired'),
            {
              action: {
                onClick: () => window.location.reload(),
                text: this.$t('core::app.reload'),
              },
            },
            30000
          )
        })

        self.$on('maintenance-mode', message => {
          self.info(
            message || 'Down for maintenance',
            {
              action: {
                onClick: () => window.location.reload(),
                text: this.$t('core::app.reload'),
              },
            },
            30000
          )
        })
      },
    })

    // Broadcasting
    if (this.appConfig.broadcasting) {
      const broadcaster = new Broadcast(this.appConfig.broadcasting)
      this.broadcaster = broadcaster
    }

    // i18n
    app.use(i18n.instance)

    // strore
    app.use(store)

    // App config
    bootApplicationConfig(this.appConfig)

    // Register component and directives
    registerDirectives(app)
    registerComponents(app)
    registerFields(app)

    // Boot app
    this.boot(app, router)

    // Voip
    if (
      this.appConfig.hasOwnProperty('voip') &&
      this.appConfig.voip.client &&
      app.config.globalProperties.$gate.userCan('use voip')
    ) {
      const VoIPInstance = new VoIP(this.appConfig.voip.client)
      app.config.globalProperties.$voip = VoIPInstance
      app.component('CallComponent', VoIPInstance.callComponent)
    }

    // Handle router
    router.beforeEach((to, from, next) => beforeEachRoute(to, from, next, app))
    app.use(router)

    this.app = app

    const vm = app.mount('#app')

    app.config.globalProperties.$dialog = {
      confirm(options) {
        // https://github.com/tailwindlabs/headlessui/issues/493
        const dialogIsOpen = document.querySelectorAll('.dialog')

        return new Promise((resolve, reject) => {
          vm.$data.confirmationDialog = Object.assign({}, options, {
            injectedInDialog: dialogIsOpen.length > 0,
            resolve: attrs => {
              resolve(attrs)
              vm.$data.confirmationDialog = null
            },
            reject: attrs => {
              reject(attrs)
              vm.$data.confirmationDialog = null
            },
          })
        })
      },
    }
  }

  /**
   * Get the application CSRF token
   *
   * @return {String|null}
   */
  csrfToken() {
    return this.appConfig.csrfToken || null
  }

  /**
   * Register a callback to be called before the application starts
   */
  booting(callback) {
    if (Array.isArray(callback)) {
      callback.forEach(c => this.booting(c))
    } else {
      this.bootingCallbacks.push(callback)
    }

    return this
  }

  /**
   * Execute all of the booting callbacks.
   */
  boot(app, router) {
    this.bootingCallbacks.forEach(callback => callback(app, router, store))
    this.bootingCallbacks = []
  }

  /**
   * Get configuration for the given key.
   *
   * @param key string
   */
  config(key) {
    return get(this.appConfig, key)
  }

  /**
   * Helper request function
   * @param  {Object} options
   *
   * @return {Object}
   */
  request(options) {
    if (options !== undefined) {
      return this.axios(options)
    }

    return this.axios
  }

  /**
   * Register global event
   * @param  {mixed} args
   *
   * @return {Void}
   */
  $on(...args) {
    this.bus.on(...args)
  }

  /**
   * Deregister event
   * @param  {mixed} args
   *
   * @return {Void}
   */
  $off(...args) {
    this.bus.off(...args)
  }

  /**
   * Emit global event
   * @param  {mixed} args
   *
   * @return {Void}
   */
  $emit(...args) {
    this.bus.emit(...args)
  }

  /**
   * Show toasted success messages
   *
   * @param {String} message
   * @param {Object} options
   * @param {Number} duration
   *
   * @return {Void}
   */
  success(message, options, duration = 4000) {
    this.app.config.globalProperties.$notify(
      Object.assign({}, options, {
        text: message,
        type: 'success',
        group: 'app',
      }),
      duration
    )
  }

  /**
   * Show toasted info messages
   *
   * @param {String} message
   * @param {Object} options
   * @param {Number} duration
   *
   * @return {Void}
   */
  info(message, options, duration = 4000) {
    this.app.config.globalProperties.$notify(
      Object.assign({}, options, {
        text: message,
        type: 'info',
        group: 'app',
      }),
      duration
    )
  }

  /**
   * Show toasted error messages
   *
   * @param {String} message
   * @param {Object} options
   * @param {Number} duration
   *
   * @return {Void}
   */
  error(message, options, duration = 4000) {
    this.app.config.globalProperties.$notify(
      Object.assign({}, options, {
        text: message,
        type: 'error',
        group: 'app',
      }),
      duration
    )
  }

  /**
   * Add new a keyboard shortcut
   *
   * @return {Void}
   */
  addShortcut(keys, callback) {
    Mousetrap.bind(keys, callback)
  }

  /**
   * Disable keyboard shortcut
   *
   * @return {Void}
   */
  disableShortcut(keys) {
    Mousetrap.unbind(keys)
  }

  /**
   * Get the global dialog instance
   *
   * @return {Object}
   */
  dialog() {
    return this.app.config.globalProperties.$dialog
  }

  /**
   * Get the global modal instance
   *
   * @return {Object}
   */
  modal() {
    return this.app.config.globalProperties.$iModal
  }
}

/**
 * Before each route callback function
 */
function beforeEachRoute(to, from, next, app) {
  // Close sidebar on route change when on mobile
  if (store.state.sidebarOpen) {
    store.commit('SET_SIDEBAR_OPEN', false)
  }

  // Check if it's a gate route, if yes, perform check before each route
  const gateRoute = to.matched.find(match => match.meta.gate)

  if (gateRoute && typeof gateRoute.meta.gate === 'string') {
    if (app.config.globalProperties.$gate.userCant(gateRoute.meta.gate)) {
      next({ path: '/403' })
    }
  }

  // Let's try to set page title now, as the user is allowed to access the route
  if (to.meta.title) {
    store.commit('SET_PAGE_TITLE', to.meta.title)
  } else if (
    store.state.pageTitle &&
    !to.meta.title &&
    // Do not set empty title on child routes
    to.matched.length === 1
  ) {
    // Reset title if now there is no title but previously title was set
    store.commit('SET_PAGE_TITLE', '')
  }

  next()
}

/**
 * Boot the application config
 */
function bootApplicationConfig(config) {
  store.commit('SET_SETTINGS', config.options ?? {})
  store.commit('SET_API_URL', config.apiURL ?? null)
  store.commit('SET_URL', config.url ?? null)
  store.commit('SET_MENU', config.menu ?? [])
}
