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
import moment from 'moment-timezone'

// import other locales as they are added
import 'moment/dist/locale/pt-br'
import 'moment/dist/locale/es'

import momentPhp from './momentPhp'
import { getLocale } from '@/utils'

moment.locale(getLocale().replace('_', '-'))

momentPhp(moment)

window.moment = moment
