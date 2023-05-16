<template>
  <div>
    <Transition
      leave-active-class="transition ease-in duration-1000"
      leave-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <small class="text-sm text-neutral-400 dark:text-neutral-200">
        <slot>
          {{ message }}
        </slot>
      </small>
    </Transition>
  </div>
</template>
<script setup>
defineProps(['message'])
</script>
