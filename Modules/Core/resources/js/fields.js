/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */
import { defineAsyncComponent } from 'vue'

import TextField from '~/Core/resources/js/components/Fields/TextField.vue'
import TextareaField from '~/Core/resources/js/components/Fields/TextareaField.vue'
import IconPickerField from '~/Core/resources/js/components/Fields/IconPickerField.vue'
const DomainField = defineAsyncComponent(() =>
  import('~/Core/resources/js/components/Fields/DomainField.vue')
)
import BooleanField from '~/Core/resources/js/components/Fields/BooleanField.vue'
import CheckboxField from '~/Core/resources/js/components/Fields/CheckboxField.vue'
import RadioField from '~/Core/resources/js/components/Fields/RadioField.vue'
import DateField from '~/Core/resources/js/components/Fields/DateField.vue'
import DateTimeField from '~/Core/resources/js/components/Fields/DateTimeField.vue'
import SelectField from '~/Core/resources/js/components/Fields/SelectField.vue'
import SelectMultipleField from '~/Core/resources/js/components/Fields/SelectMultipleField.vue'
import DropdownSelectField from '~/Core/resources/js/components/Fields/DropdownSelectField.vue'
import EditorField from '~/Core/resources/js/components/Fields/EditorField.vue'
import NumericField from '~/Core/resources/js/components/Fields/NumericField.vue'
import BelongsToField from '~/Core/resources/js/components/Fields/BelongsToField.vue'
import TimezoneField from '~/Core/resources/js/components/Fields/TimezoneField.vue'
import IntroductionField from '~/Core/resources/js/components/Fields/IntroductionField.vue'
import ColorSwatchesField from '~/Core/resources/js/components/Fields/ColorSwatchesField.vue'
import ReminderField from '~/Core/resources/js/components/Fields/ReminderField.vue'
import VisibilityGroupField from '~/Core/resources/js/components/Fields/VisibilityGroupField.vue'

export default function (app) {
  app
    .component('TextField', TextField)
    .component('TextareaField', TextareaField)
    .component('IconPickerField', IconPickerField)
    .component('DomainField', DomainField)
    .component('BooleanField', BooleanField)
    .component('CheckboxField', CheckboxField)
    .component('RadioField', RadioField)
    .component('DateField', DateField)
    .component('DateTimeField', DateTimeField)
    .component('SelectField', SelectField)
    .component('SelectMultipleField', SelectMultipleField)
    .component('DropdownSelectField', DropdownSelectField)
    .component('EditorField', EditorField)
    .component('NumericField', NumericField)
    .component('BelongsToField', BelongsToField)
    .component('TimezoneField', TimezoneField)
    .component('IntroductionField', IntroductionField)
    .component('ColorSwatchesField', ColorSwatchesField)
    .component('ReminderField', ReminderField)
    .component('VisibilityGroupField', VisibilityGroupField)
}
