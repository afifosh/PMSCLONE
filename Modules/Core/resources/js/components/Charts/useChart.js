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
import { computed } from 'vue'
import map from 'lodash/map'

export function useChart(result) {
  /**
   * Check whether there is a chart data to display
   */
  const hasData = computed(() => {
    const data = chartData.value
    const totalSeries = data.series.length

    if (totalSeries === 0) {
      return false
    }

    let anySerieHasData = false
    for (let i = 0; i < totalSeries; i++) {
      if (data.series[i].length > 0) {
        anySerieHasData = data.series[i].some(val => val.value > 0)

        if (anySerieHasData) {
          break
        }
      }
    }
    return anySerieHasData
  })

  /**
   * Get the chart ready data
   */
  const chartData = computed(() => {
    return {
      labels: map(result.value, data => data.label),
      series: [
        map(result.value, data => {
          return {
            meta: data.label,
            value: data.value,
            color: data.color,
          }
        }),
      ],
    }
  })

  return { chartData, hasData }
}
