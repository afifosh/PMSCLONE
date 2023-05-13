<template>
  <div v-show="text">
    <IButton
      :size="false"
      variant="white"
      class="mt-4 inline-flex rounded-md px-1.5 py-0"
      icon="DotsHorizontal"
      v-i-tooltip="$t('mailclient::mail.trimmed_content')"
      v-i-toggle="textId"
    />
    <div class="mt-5 hidden" :id="textId">
      <HtmlableLightbox :html="text" />
    </div>
  </div>
</template>
<script setup>
import { randomString } from '@/utils'
import HtmlableLightbox from '~/Core/resources/js/components/Lightbox/HtmlableLightbox.vue'

const props = defineProps({
  text: { required: true },
})

const textId = randomString()
</script>
