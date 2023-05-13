<template>
  <!-- If recipients is empty, it can be a draft message with not yet added e.q. to headers -->
  <div v-show="showRecipients">
    <p class="space-x-1 text-sm text-neutral-800 dark:text-neutral-100">
      <span class="font-semibold">{{ label }}:</span>
      <span v-for="recipient in wrappedRecipients">
        <MailRecipient :recipient="recipient" />

        <span
          v-if="!hasRecipients"
          v-text="'(' + $t('mailclient::inbox.unknown_address') + ')'"
        />
      </span>
    </p>
  </div>
</template>
<script setup>
import { computed } from "vue";
import castArray from "lodash/castArray";
import MailRecipient from "./MessageRecipient.vue";

const props = defineProps({
  showWhenEmpty: { default: true, type: Boolean },
  label: String,
  recipients: {},
});

const wrappedRecipients = computed(() => castArray(props.recipients));

const hasRecipients = computed(
  () => !props.recipients || wrappedRecipients.value.length > 0
);

const showRecipients = computed(() => {
  if (props.showWhenEmpty) {
    return true;
  }

  if (!hasRecipients.value && props.showWhenEmpty === false) {
    return false;
  }

  return true;
});
</script>
