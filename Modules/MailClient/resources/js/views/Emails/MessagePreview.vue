<template>
  <div class="mail-text all-revert" v-once>
    <div class="font-sans text-sm leading-[initial] dark:text-white">
      <HtmlableLightbox :html="visibleText" />
      <HiddenText :text="hiddenText" />
    </div>
  </div>
</template>
<script setup>
import HiddenText from './MessageHiddenText.vue'
import HtmlableLightbox from '~/Core/resources/js/components/Lightbox/HtmlableLightbox.vue'
defineProps({
  visibleText: null,
  hiddenText: null,
})
</script>
