<template>
  <IButton
    @click="$emit('update:collapsed', !collapsed)"
    variant="white"
    size="sm"
    :text="collapseText"
  />
</template>
<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

defineEmits(['update:collapsed'])

const props = defineProps({
  collapsed: { required: true, type: Boolean },
})

const { t } = useI18n()

const collapseText = computed(() =>
  props.collapsed ? t('core::fields.more') : t('core::fields.less')
)
</script>
