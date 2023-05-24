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
// https://stackoverflow.com/questions/36125038/generate-array-of-times-as-strings-for-every-x-minutes-in-javascript
const timelineLabels = (
  desiredStartTime,
  interval,
  period,
  format = 'hh:mm A',
  maxHour = null
) => {
  const periodsInADay = moment.duration(1, 'day').as(period)

  const timeLabels = []
  const startTimeMoment = moment(desiredStartTime, 'hh:mm')

  if (maxHour) {
    maxHour = moment(maxHour + ':00', 'HH:mm a')
  }

  for (let i = 0; i < periodsInADay; i += interval) {
    startTimeMoment.add(i === 0 ? 0 : interval, period)

    if (
      !maxHour ||
      (maxHour && startTimeMoment.isSameOrBefore(maxHour, 'hour'))
    ) {
      timeLabels.push(startTimeMoment.format(format))
    }
  }

  return timeLabels
}

export default timelineLabels
