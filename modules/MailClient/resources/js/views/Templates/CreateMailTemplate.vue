<template>
  <TemplateForm :form="form">
    <template #bottom>
      <div
        class="space-x-2 divide-y divide-neutral-200 border-t border-neutral-200 pt-3 text-right dark:divide-neutral-700 dark:border-neutral-700"
      >
        <IButton
          variant="white"
          :text="$t('core::app.cancel')"
          @click="$emit('cancel-requested')"
        />
        <IButton
          type="submit"
          variant="primary"
          @click="create"
          :text="$t('core::app.create')"
        />
      </div>
    </template>
  </TemplateForm>
</template>
<script setup>
import TemplateForm from './MailTemplateForm.vue'
import { useI18n } from 'vue-i18n'
import { useForm } from '~/Core/resources/js/composables/useForm'
import { useMailTemplates } from '../../composables/useMailTemplates'

const emit = defineEmits(['created', 'cancel-requested'])

const { t } = useI18n()
const { addTemplate } = useMailTemplates()

const { form } = useForm({
  name: null,
  body: null,
  subject: null,
  is_shared: true,
})

function create() {
  form.post('/mails/templates').then(template => {
    addTemplate(template)

    emit('created', template)

    Innoclapps.success(t('mailclient::mail.templates.created'))
  })
}
</script>
