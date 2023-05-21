<template>
  <ICard
    :header="$t('contacts::company.companies')"
    no-body
    :overlay="!componentReady"
  >
    <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
      <li class="px-4 py-4 sm:px-6">
        <div
          class="space-x-0 space-y-3 md:flex md:items-center md:justify-between md:space-y-0 lg:space-x-3"
        >
          <div>
            <h5
              class="font-medium leading-relaxed text-neutral-700 dark:text-neutral-100"
              v-t="
                'contacts::company.settings.automatically_associate_with_contacts'
              "
            />
            <p
              class="break-words text-sm text-neutral-600 dark:text-neutral-300"
              v-t="
                'contacts::company.settings.automatically_associate_with_contacts_info'
              "
            />
          </div>
          <div>
            <IFormToggle
              :value="true"
              :unchecked-value="false"
              @change="submit"
              v-model="form.auto_associate_company_to_contact"
            />
          </div>
        </div>
      </li>
    </ul>
  </ICard>
</template>
<script setup>
import { useSettings } from '~/Core/resources/js/views/Settings/useSettings'

const { form, submit, isReady: componentReady } = useSettings()
</script>
