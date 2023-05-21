<template>
  <div>
    <Editor
      v-model="form.body"
      :with-mention="true"
      @input="form.errors.clear('body')"
    />

    <IFormError v-text="form.getError('body')" />

    <div class="mt-2 space-x-2 text-right">
      <IButton
        variant="white"
        @click="$emit('cancelled')"
        size="sm"
        :text="$t('core::app.cancel')"
      />
      <IButton
        variant="secondary"
        @click="update"
        size="sm"
        :disabled="form.busy"
        :text="$t('core::app.save')"
      />
    </div>
  </div>
</template>
<script setup>
import { useRecordStore } from '~/Core/resources/js/composables/useRecordStore'
import { useI18n } from 'vue-i18n'
import { useForm } from '~/Core/resources/js/composables/useForm'

const emit = defineEmits(['updated', 'cancelled'])

const props = defineProps({
  commentId: { required: true, type: Number },
  body: { required: true, type: String },
  commentableType: { required: true, type: String },
  commentableId: { required: true, type: Number },
  viaResource: { type: String },
})

const { t } = useI18n()

const {
  record,
  updateResourceRecordHasManyRelationship,
  updateResourceRecordSubRelation,
} = useRecordStore()

const { form } = useForm({
  body: props.body,
})

/**
 * Update comment in store when displayed via resource
 */
function updateCommentInStoreWhenViaResource(comment) {
  updateResourceRecordSubRelation(
    props.commentableType,
    props.commentableId,
    'comments',
    comment
  )
}

/**
 * Update comment in store
 */
function updateCommentInStore(comment) {
  updateResourceRecordHasManyRelationship(comment, 'comments')
}

/**
 * Update the current comment
 */
function update() {
  if (props.viaResource) {
    form.withQueryString({
      via_resource: props.viaResource,
      via_resource_id: record.value.id,
    })
  }

  form.put(`/comments/${props.commentId}`).then(comment => {
    props.viaResource
      ? updateCommentInStoreWhenViaResource(comment)
      : updateCommentInStore(comment)

    emit('updated', comment)

    Innoclapps.success(t('comments::comment.updated'))
  })
}
</script>
