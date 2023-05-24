import { computed } from 'vue'
import { isISODate, isStandardDateTime, isDate } from '@/utils'
import { useApp } from './useApp'

export function useDates() {
  const { currentUser, setting } = useApp()

  /**
   * Determine if the user is using 12-hour time
   */
  const usesTwelveHourTime = computed(() => {
    return currentTimeFormat.value.indexOf('H:i') === -1
  })

  /**
   * Get the user's local timezone.
   */
  const userTimezone = computed(() => {
    return currentUser.value && currentUser.value.timezone
      ? currentUser.value.timezone
      : moment.tz.guess()
  })

  /**
   * Determine the application current time format
   */
  const currentTimeFormat = computed(() => {
    return currentUser.value
      ? currentUser.value.time_format
      : setting('time_format')
  })

  /**
   * Determine the application current date format
   */
  const currentDateFormat = computed(() => {
    return currentUser.value
      ? currentUser.value.date_format
      : setting('date_format')
  })

  /**
   * Converts the PHP options date format to moment compatible
   */
  const dateTimeFormatForMoment = computed(() => {
    return moment().PHPconvertFormat(
      currentDateFormat.value + ' ' + currentTimeFormat.value
    )
  })

  /**
   * Converts the PHP options date format to moment compatible
   */
  const dateFormatForMoment = computed(() => {
    return moment().PHPconvertFormat(currentDateFormat.value)
  })

  /**
   * Convert the given localized date time string to the application's timezone.
   */
  function dateToAppTimezone(value, format = 'YYYY-MM-DD HH:mm:ss') {
    return value
      ? moment
          .tz(value, userTimezone.value)
          .clone()
          .tz(Innoclapps.config('timezone'))
          .format(format)
      : value
  }

  /**
   * Convert the given application timezone date time string to the local timezone.
   */
  function dateFromAppTimezone(value, format = 'YYYY-MM-DD HH:mm:ss') {
    if (!value) {
      return value
    }

    return appMoment(value).clone().tz(userTimezone.value).format(format)
  }

  /**
   * Get the localized date by UTC/App default datetime
   */
  function localizedDateTime(value, format) {
    if (!value) {
      return value
    }

    return appMoment(value)
      .clone()
      .tz(userTimezone.value)
      .format(format || dateTimeFormatForMoment.value)
  }

  /**
   * Get the localized date by UTC/App default date
   */
  function localizedDate(value, format) {
    if (!value) {
      return value
    }

    return appMoment(value)
      .clone()
      .tz(userTimezone.value)
      .format(format || dateFormatForMoment.value)
  }

  /**
   * Get app date now with app timezone set
   */
  function appMoment(value) {
    return moment.tz(value, Innoclapps.config('timezone'))
  }

  /**
   * Get current application time and date
   */
  function appDate(format = 'YYYY-MM-DD HH:mm:ss') {
    return appMoment().format(format)
  }

  function maybeFormatDateValue(value) {
    if (isDate(value)) {
      return localizedDate(value)
    } else if (isStandardDateTime(value)) {
      return localizedDateTime(value)
    } else if (isISODate(value)) {
      if (/T00:00:00.000000Z$/.test(value)) {
        return localizedDate(value)
      }

      return localizedDateTime(value)
    }

    return value
  }

  return {
    appDate,
    appMoment,
    localizedDate,
    localizedDateTime,
    dateFromAppTimezone,
    dateToAppTimezone,
    dateFormatForMoment,
    dateTimeFormatForMoment,
    currentDateFormat,
    currentTimeFormat,
    userTimezone,
    usesTwelveHourTime,
    maybeFormatDateValue,
  }
}
