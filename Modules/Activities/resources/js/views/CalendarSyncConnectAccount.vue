<template>
  <IModal
    size="sm"
    id="calendarConnectNewAccount"
    hide-footer
    :title="$t('core::oauth.connect_new_account')"
  >
    <div class="py-4">
      <p
        class="mb-5 text-center text-neutral-800 dark:text-neutral-200"
        v-t="'activities::calendar.choose_oauth_account'"
      />
      <div class="flex justify-center space-x-2">
        <div
          class="flex cursor-pointer flex-col items-center space-y-1 rounded-lg border border-neutral-200 px-5 py-3 shadow-sm hover:bg-neutral-100 dark:border-neutral-500 dark:hover:bg-neutral-800"
          @click="connectOAuthAccount('google')"
        >
          <IPopover
            ref="googlePopoverRef"
            class="max-w-xs sm:max-w-sm"
            :disabled="isGoogleApiConfigured()"
            placement="top"
          >
            <GoogleIcon
              @click="
                isGoogleApiConfigured()
                  ? connectOAuthAccount('google')
                  : undefined
              "
            />

            <template #popper>
              <div class="px-4 py-3 text-sm">
                <p class="whitespace-pre-wrap">
                  {{ $t('activities::calendar.missing_google_integration') }}
                </p>
                <RouterLink
                  v-if="$gate.isSuperAdmin()"
                  href="/settings/integrations/google"
                  class="link mt-2 block text-right"
                  :to="{ name: 'settings-integrations-google' }"
                  v-t="'core::settings.go_to_settings'"
                />
              </div>
            </template>
          </IPopover>
          <span
            class="text-sm font-medium text-neutral-600 dark:text-neutral-300"
          >
            Google Calendar
          </span>
        </div>
        <div
          class="flex cursor-pointer flex-col items-center space-y-1 rounded-lg border border-neutral-200 px-5 py-3 shadow-sm hover:bg-neutral-100 dark:border-neutral-500 dark:hover:bg-neutral-800"
          @click="connectOAuthAccount('microsoft')"
        >
          <IPopover
            placement="top"
            class="max-w-xs sm:max-w-sm"
            :disabled="isMicrosoftGraphConfigured()"
            ref="microsoftPopoverRef"
          >
            <OutlookIcon
              @click="
                isMicrosoftGraphConfigured()
                  ? connectOAuthAccount('microsoft')
                  : undefined
              "
            />
            <template #popper>
              <div class="px-4 py-3 text-sm">
                <p class="whitespace-pre-wrap">
                  {{ $t('activities::calendar.missing_outlook_integration') }}
                </p>
                <RouterLink
                  v-if="$gate.isSuperAdmin()"
                  href="/settings/integrations/google"
                  class="link mt-2 block text-right"
                  :to="{ name: 'settings-integrations-microsoft' }"
                  v-t="'core::settings.go_to_settings'"
                />
              </div>
            </template>
          </IPopover>
          <span
            class="text-sm font-medium text-neutral-600 dark:text-neutral-300"
          >
            Outlook Calendar
          </span>
        </div>
      </div>
    </div>
  </IModal>
</template>
<script setup>
import { ref } from 'vue'
import OutlookIcon from '~/Core/resources/js/components/Icons/OutlookIcon.vue'
import GoogleIcon from '~/Core/resources/js/components/Icons/GoogleIcon.vue'
import { useApp } from '~/Core/resources/js/composables/useApp'

const { isGoogleApiConfigured, isMicrosoftGraphConfigured } = useApp()

const googlePopoverRef = ref(null)
const microsoftPopoverRef = ref(null)

function connectOAuthAccount(provider) {
  if (provider === 'google' && !isGoogleApiConfigured()) {
    googlePopoverRef.value.show()
    return
  } else if (provider === 'microsoft' && !isMicrosoftGraphConfigured()) {
    microsoftPopoverRef.value.show()
    return
  }

  window.location.href = `${Innoclapps.config(
    'url'
  )}/calendar/sync/${provider}/connect`
}
</script>
