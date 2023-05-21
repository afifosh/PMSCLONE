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
import { computed } from 'vue'
import { useStore } from 'vuex'

// resourceId - resourceName
export function useComments(commentableId, commentableType) {
  const store = useStore()

  const requestInProgress = computed(
    () => store.state.comments.requestInProgress
  )

  const commentsAreLoaded = computed({
    set(newValue) {
      store.commit('comments/SET_LOADED', {
        commentableId: commentableId,
        commentableType: commentableType,
        value: newValue,
      })
    },
    get() {
      return store.getters['comments/areLoadedFor'](
        commentableId,
        commentableType
      )
    },
  })

  const commentsAreVisible = computed({
    set(newValue) {
      store.commit('comments/SET_VISIBILITY', {
        commentableId: commentableId,
        commentableType: commentableType,
        visible: newValue,
      })
    },
    get() {
      return store.getters['comments/areVisibleFor'](
        commentableId,
        commentableType
      )
    },
  })

  const commengIsBeingCreated = computed({
    set(newValue) {
      store.commit('comments/SET_ADD_COMMENT_VISIBILITY', {
        commentableId: commentableId,
        commentableType: commentableType,
        value: newValue,
      })
    },
    get() {
      return store.getters['comments/isCommentBeingCreatedFor'](
        commentableId,
        commentableType
      )
    },
  })

  function toggleCommentsVisibility() {
    commentsAreVisible.value = !commentsAreVisible.value
  }

  function getAllComments(viaResource, viaResourceId) {
    return store.dispatch('comments/getAll', {
      resourceName: commentableType,
      resourceId: commentableId,
      viaResource: viaResource,
      viaResourceId: viaResourceId,
    })
  }

  return {
    requestInProgress,
    commengIsBeingCreated,
    commentsAreLoaded,
    commentsAreVisible,
    getAllComments,
    toggleCommentsVisibility,
  }
}
