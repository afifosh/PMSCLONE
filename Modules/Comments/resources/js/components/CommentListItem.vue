<template>
  <div>
    <!-- The :id="'comment-'+commentId" is used to auto focus the comment in CommentsList.vue -->
    <div
      :id="'comment-' + commentId"
      :class="[
        'comment rounded-md ',
        highlighted
          ? 'bg-info-50 dark:bg-neutral-800'
          : 'bg-white dark:bg-neutral-700',
        {
          'border border-neutral-300 px-4 py-2.5 shadow-sm dark:border-neutral-600':
            !commentBeingEdited,
        },
      ]"
    >
      <div class="flex flex-wrap" v-show="!commentBeingEdited">
        <div class="grow" v-once>
          <IAvatar size="xs" class="mr-1" :src="creator.avatar_url" />
          <i18n-t
            scope="global"
            :keypath="'comments::comment.user_left_comment'"
            tag="span"
            class="text-sm text-neutral-800 dark:text-white"
          >
            <template #user>
              <b class="font-medium" v-text="creator.name"></b>
            </template>
          </i18n-t>
        </div>
        <div class="mt-1 text-sm text-neutral-500 dark:text-neutral-300" v-once>
          {{ localizedDateTime(createdAt) }}
        </div>
      </div>
      <HtmlableLightbox
        v-show="!commentBeingEdited"
        v-memo="[commentBeingEdited]"
        class="wysiwyg-text mt-3"
        :html="body"
      />
      <EditComment
        v-if="commentBeingEdited"
        class="mt-3"
        :comment-id="commentId"
        :body="body"
        :commentable-type="commentableType"
        :commentable-id="commentableId"
        :via-resource="viaResource"
        @cancelled="commentBeingEdited = false"
        @updated="commentBeingEdited = false"
      />
    </div>
    <div class="flex justify-end space-x-2 py-2 text-sm">
      <a
        v-if="createdBy !== currentUser.id && !commentBeingEdited"
        href="#"
        class="link"
        v-t="'comments::comment.reply'"
        @click.prevent="replyToComment"
      />
      <a
        v-show="authorizations.update && !commentBeingEdited"
        href="#"
        class="link"
        v-t="'core::app.edit'"
        @click.prevent="commentBeingEdited = true"
      />
      <a
        v-show="authorizations.delete && !commentBeingEdited"
        href="#"
        class="text-danger-500 hover:text-danger-700"
        v-t="'core::app.delete'"
        @click.prevent="destroy(commentId)"
      />
    </div>
  </div>
</template>
<script setup>
import { ref, computed, nextTick } from 'vue'
import EditComment from './EditComment.vue'
import { useDates } from '~/Core/resources/js/composables/useDates'
import { useComments } from '../composables//useComments'
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useApp } from '~/Core/resources/js/composables/useApp'
import HtmlableLightbox from '~/Core/resources/js/components/Lightbox/HtmlableLightbox.vue'

const emit = defineEmits(['deleted'])

const props = defineProps({
  commentId: { required: true, type: Number },
  body: { required: true, type: String },
  createdBy: { required: true, type: Number },
  createdAt: { required: true, type: String },
  authorizations: { required: true, type: Object },
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  highlighted: Boolean,
  viaResource: { type: String },
})

const { localizedDateTime } = useDates()
const { currentUser, findUserById } = useApp()
const creator = computed(() => findUserById(props.createdBy))

const { commengIsBeingCreated } = useComments(
  props.commentableId,
  props.commentableType
)

const {
  removeResourceRecordHasManyRelationship,
  removeResourceRecordSubRelation,
} = useRecordStore()

const commentBeingEdited = ref(false)

/**
 * Initialize a reply to the current comment
 */
function replyToComment() {
  commengIsBeingCreated.value = true

  nextTick(() => {
    const $addCommentWrapper = document.getElementById(
      'add-comment-' + props.commentableType + '-' + props.commentableId
    )

    $addCommentWrapper.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
      inline: 'nearest',
    })

    // Add timeout untill editor is initialized
    setTimeout(() => {
      tinymce.activeEditor.setContent('')

      tinymce.activeEditor.concordCommands.insertMentionUser(
        creator.value.id,
        creator.value.name
      )
    }, 650)
  })
}

/**
 * Remove the current comment from store when displayed via resource
 */
function removeCommentFromStoreWhenViaResource() {
  removeResourceRecordSubRelation(
    props.commentableType,
    props.commentableId,
    'comments',
    props.commentId
  )
}

/**
 * Remove the current comment from store
 */
function removeCommentFromStore() {
  removeResourceRecordHasManyRelationship(props.commentId, 'comments')
}

async function destroy(id) {
  await Innoclapps.dialog().confirm()
  await Innoclapps.request().delete(`/comments/${id}`)

  emit('deleted', props.comment)

  props.viaResource
    ? removeCommentFromStoreWhenViaResource()
    : removeCommentFromStore()
}
</script>
