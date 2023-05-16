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
import { ref, unref, isRef, watch, watchEffect } from 'vue'
import { tryOnBeforeUnmount } from '@vueuse/core'

export function useNotification(userId, callback) {
  const canListen = Innoclapps.broadcaster?.hasDriver() || false

  if (!canListen) {
    return
  }

  window.Echo.private('Modules.Users.Models.User.' + userId).notification(
    callback
  )
}

export function usePrivateChannel(channel, event, callback) {
  const canListen = Innoclapps.broadcaster?.hasDriver() || false

  const channelName = ref(null)

  watchEffect(() => {
    channelName.value = isRef(channel) ? unref(channel) : channel
  })

  watch(
    channelName,
    (newChannel, oldChannel) => {
      // console.log('watch effect triggered ' + channelName.value)
      if (oldChannel) {
        stop(oldChannel)
      }

      if (newChannel) {
        start()
      }
    },
    { immediate: true }
  )

  function start() {
    if (!canListen || !channelName.value) return
    // console.log('start ' + channelName.value)
    window.Echo.private(channelName.value).listen(event, callback)
  }

  function stop(ch) {
    if (!canListen || (!ch && !channelName.value)) return
    // console.log('stop ' + (ch || channelName.value))
    window.Echo.private(ch || channelName.value).stopListening(event, callback)
  }

  tryOnBeforeUnmount(stop)

  return { start, stop }
}
