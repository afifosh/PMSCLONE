/**
 * Academy Dashboard charts and datatable
 */

'use strict';

// Hour pie chart

(function () {
  let labelColor, headingColor, borderColor;

  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }

  // Donut Chart Colors
  const chartColors = {
    donut: {
      series1: '#22A95E',
      series2: '#24B364',
      series3: config.colors.success,
      series4: '#53D28C',
      series5: '#7EDDA9',
      series6: '#A9E9C5'
    }
  };

  // datatbale bar chart

  const horizontalBarChartEl = document.querySelector('#contracts-by-type'),
    contractsByValueEl = document.querySelector('#contracts-by-value'),
    // horizontalBarChartConfig = {
    contractsByTypeConfig = {
      chart: {
        height: 270,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: '70%',
          distributed: true,
          startingShape: 'rounded',
          borderRadius: 7
        }
      },
      grid: {
        strokeDashArray: 10,
        borderColor: borderColor,
        xaxis: {
          lines: {
            show: true
          }
        },
        yaxis: {
          lines: {
            show: false
          }
        },
        padding: {
          top: -35,
          bottom: -12
        }
      },

      colors: [
        config.colors.primary,
        config.colors.info,
        config.colors.success,
        config.colors.secondary,
        config.colors.danger,
        config.colors.warning
      ],
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff'],
          fontWeight: 200,
          fontSize: '13px',
          fontFamily: 'Public Sans'
        },
        formatter: function (val, opts) {
          return (
            contractsByTypeConfig.labels[opts.dataPointIndex] + ': (' + contractsByType[opts.dataPointIndex].total + ')'
          );
        },
        offsetX: 0,
        dropShadow: {
          enabled: false
        }
      },
      labels: contractsByType.map(item => item.name),
      series: [
        {
          data: contractsByType.map(item => item.percentage)
        }
      ],

      xaxis: {
        categories: contractsByType.map(item => item.name),
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          },
          formatter: function (val) {
            return `${val}%`;
          }
        }
      },
      yaxis: {
        max: 100,
        labels: {
          style: {
            colors: [labelColor],
            fontFamily: 'Public Sans',
            fontSize: '13px'
          }
        }
      },
      tooltip: {
        enabled: true,
        style: {
          fontSize: '12px'
        },
        onDatasetHover: {
          highlightDataSeries: false
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + '%</span>' + '</div>';
        }
      },
      legend: {
        show: true
      }
    };
  if (typeof horizontalBarChartEl !== undefined && horizontalBarChartEl !== null) {
    const horizontalBarChart = new ApexCharts(horizontalBarChartEl, contractsByTypeConfig);
    horizontalBarChart.render();
  }

  const contractsByValueConfig = {
    chart: {
      height: 270,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '70%',
        distributed: true,
        startingShape: 'rounded',
        borderRadius: 7
      }
    },
    grid: {
      strokeDashArray: 10,
      borderColor: borderColor,
      xaxis: {
        lines: {
          show: true
        }
      },
      yaxis: {
        lines: {
          show: false
        }
      },
      padding: {
        top: -35,
        bottom: -12
      }
    },

    colors: [
      config.colors.primary,
      config.colors.info,
      config.colors.success,
      config.colors.secondary,
      config.colors.danger,
      config.colors.warning
    ],
    dataLabels: {
      enabled: true,
      style: {
        colors: ['#fff'],
        fontWeight: 200,
        fontSize: '13px',
        fontFamily: 'Public Sans'
      },
      formatter: function (val, opts) {
        return (
          contractsByValueConfig.labels[opts.dataPointIndex] + ': (' + contractsByValue[opts.dataPointIndex].total + ')'
        );
      },
      offsetX: 0,
      dropShadow: {
        enabled: false
      }
    },
    labels: contractsByValue.map(item => item.name),
    series: [
      {
        data: contractsByValue.map(item => item.percentage)
      }
    ],

    xaxis: {
      categories: contractsByValue.map(item => item.name),
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        style: {
          colors: labelColor,
          fontSize: '13px'
        },
        formatter: function (val) {
          return `${val}%`;
        }
      }
    },
    yaxis: {
      max: 100,
      labels: {
        style: {
          colors: [labelColor],
          fontFamily: 'Public Sans',
          fontSize: '13px'
        }
      }
    },
    tooltip: {
      enabled: true,
      style: {
        fontSize: '12px'
      },
      onDatasetHover: {
        highlightDataSeries: false
      },
      custom: function ({ series, seriesIndex, dataPointIndex, w }) {
        return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + '%</span>' + '</div>';
      }
    },
    legend: {
      show: true
    }
  };
  if (typeof contractsByValueEl !== undefined && contractsByValueEl !== null) {
    const contractsByValueChart = new ApexCharts(contractsByValueEl, contractsByValueConfig);
    contractsByValueChart.render();
  }

  // companies by projects
  const companiesByProjectsEl = document.querySelector('#comapaniesByProjectsChart');
  const companiesByProjectsConfig = {
    chart: {
      height: 270,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '70%',
        distributed: true,
        startingShape: 'rounded',
        borderRadius: 7
      }
    },
    grid: {
      strokeDashArray: 10,
      borderColor: borderColor,
      xaxis: {
        lines: {
          show: true
        }
      },
      yaxis: {
        lines: {
          show: false
        }
      },
      padding: {
        top: -35,
        bottom: -12
      }
    },

    colors: [
      config.colors.primary,
      config.colors.info,
      config.colors.success,
      config.colors.secondary,
      config.colors.danger,
      config.colors.warning
    ],
    dataLabels: {
      enabled: true,
      style: {
        colors: ['#fff'],
        fontWeight: 200,
        fontSize: '13px',
        fontFamily: 'Public Sans'
      },
      formatter: function (val, opts) {
        return (
          companiesByProjectsConfig.labels[opts.dataPointIndex] +
          ': (' +
          companiesByProjects[opts.dataPointIndex].total +
          ')'
        );
      },
      offsetX: 0,
      dropShadow: {
        enabled: false
      }
    },
    labels: companiesByProjects.map(item => item.name),
    series: [
      {
        data: companiesByProjects.map(item => item.percentage)
      }
    ],

    xaxis: {
      categories: companiesByProjects.map(item => item.name),
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        style: {
          colors: labelColor,
          fontSize: '13px'
        },
        formatter: function (val) {
          return `${val}%`;
        }
      }
    },
    yaxis: {
      max: 100,
      labels: {
        style: {
          colors: [labelColor],
          fontFamily: 'Public Sans',
          fontSize: '13px'
        }
      }
    },
    tooltip: {
      enabled: true,
      style: {
        fontSize: '12px'
      },
      onDatasetHover: {
        highlightDataSeries: false
      },
      custom: function ({ series, seriesIndex, dataPointIndex, w }) {
        return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + '%</span>' + '</div>';
      }
    },
    legend: {
      show: true
    }
  };
  if (typeof companiesByProjectsEl !== undefined && companiesByProjectsEl !== null) {
    const companiesByProjectsChart = new ApexCharts(companiesByProjectsEl, companiesByProjectsConfig);
    companiesByProjectsChart.render();
  }

  var topContractsByValueConfig = {
    tooltipLabels: topContractsByValue.map(function (item) {
      return item.subject;
    }),
    printable_values: topContractsByValue.map(function (item) {
      return item.printable_value;
    }),
    series: [
      {
        // name: 'Contracts By Value',
        data: topContractsByValue.map(function (item) {
          return item.value;
        })
      }
    ],
    chart: {
      height: 350,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 10,
        dataLabels: {
          position: 'top' // top, center, bottom
        },
        distributed: false
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val, data) {
        return val > 0 ? topContractsByValueConfig.printable_values[data.dataPointIndex] : '';
      },
      offsetY: -20,
      style: {
        fontSize: '12px',
        colors: [labelColor]
      }
    },
    legend: {
      show: false
    },
    tooltip: {
      enabled: true,
      shared: false, // Set to true if you want to show a single tooltip for all series data points at a particular category
      custom: function ({ series, seriesIndex, dataPointIndex, w }) {
        return (
          '<div class="card p-2">' +
          '<span><b>Contract:</b> ' +
          topContractsByValueConfig.tooltipLabels[seriesIndex] +
          '</span>' +
          '<span><b>Value:</b> ' +
          topContractsByValueConfig.printable_values[seriesIndex] +
          '</span>' +
          '</div>'
        );
      }
    },
    xaxis: {
      categories: topContractsByValue.map(function (item) {
        return item.id;
      }),
      position: 'bottom',
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      crosshairs: {
        fill: {
          type: 'gradient',
          gradient: {
            colorFrom: '#D8E3F0',
            colorTo: '#BED1E6',
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5
          }
        }
      },
      labels: {
        show: true,
        style: {
          colors: labelColor,
          fontSize: '13px'
        },
        formatter: function (val) {
          return val ? `Contract#${val}` : '';
        }
      }
    },
    yaxis: {
      axisBorder: {
        show: true
      },
      axisTicks: {
        show: true
      },
      labels: {
        show: true,
        style: {
          colors: labelColor,
          fontSize: '13px'
        }
        // formatter: function (val) {
        //   return val ;
        // }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector('#topContractsByValue'), topContractsByValueConfig);
  chart.render();

  // contracts by cycle time

  var contractsByCycleTimeConfig = {
    series: [
      {
        // name: 'Contracts By Value',
        data: contractsByCycleTime.map(function (item) {
          return item.contract_count;
        })
      }
    ],
    chart: {
      height: 350,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 10,
        dataLabels: {
          position: 'top' // top, center, bottom
        },
        distributed: false
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val > 0 ? val : '';
      },
      offsetY: -20,
      style: {
        fontSize: '12px',
        colors: [labelColor]
      }
    },
    legend: {
      show: false
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: contractsByCycleTime.map(function (item) {
        return item.time_period;
      }),
      position: 'bottom',
      labels: {
        show: true,
        style: {
          fontSize: '12px',
          colors: [labelColor]
        }
      }
    },
    yaxis: {
      axisBorder: {
        show: true
      },
      axisTicks: {
        show: true
      },
      labels: {
        show: true,
        style: {
          colors: labelColor,
          fontSize: '13px'
        }
        // formatter: function (val) {
        //   return val + "%";
        // }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector('#contractsByCycleTime'), contractsByCycleTimeConfig);
  chart.render();

  // contracts by expiry time
  var contractsByExpiryTimeConfig = {
    series: [
      {
        // name: 'Contracts By Value',
        data: contractsByExpiryTime.map(function (item) {
          return item.contract_count;
        })
      }
    ],
    chart: {
      height: 350,
      type: 'bar',
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 10,
        dataLabels: {
          position: 'top' // top, center, bottom
        },
        distributed: false
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val > 0 ? val : '';
      },
      offsetY: -20,
      style: {
        fontSize: '12px',
        colors: [labelColor]
      }
    },
    legend: {
      show: false
    },
    tooltip: {
      enabled: false
    },
    xaxis: {
      categories: contractsByExpiryTime.map(function (item) {
        return item.time_period;
      }),
      position: 'bottom',
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      tooltip: {
        enabled: false
      },
      labels: {
        show: true,
        style: {
          fontSize: '12px',
          colors: [labelColor]
        }
      }
    },
    yaxis: {
      axisBorder: {
        show: true
      },
      axisTicks: {
        show: true
      },
      labels: {
        show: true,
        style: {
          colors: labelColor,
          fontSize: '13px'
        }
        // formatter: function (val) {
        //   return val + "%";
        // }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector('#contractsByExpiryTime'), contractsByExpiryTimeConfig);
  chart.render();

  // contracts by statuses
  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }
  //   const chartColors = {
  //   donut: {
  //     series1: config.colors.warning,
  //     series2: config.colors.danger,
  //     series3: config.colors.success,
  //     series4: config.colors.secondary,
  //     series5: config.colors.info,
  //     series6: '#3a622d',
  //   }
  // };
  // Generated Leads Chart
  // --------------------------------------------------------------------
  const contractsByStatusEl = document.querySelector('#contractsByStatus'),
    contractsByStatusConfig = {
      chart: {
        height: 400,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: Object.keys(contractsByStatus).filter(key => key !== 'total'),
      series: Object.keys(contractsByStatus)
        .filter(key => key !== 'total')
        .map(key => contractsByStatus[key]),
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series2,
        chartColors.donut.series3,
        chartColors.donut.series4,
        chartColors.donut.series5,
        chartColors.donut.series6
      ],
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: false,
        formatter: function (val, opt) {
          return parseInt(val);
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        offsetY: 10,
        markers: {
          width: 8,
          height: 8,
          offsetX: -3
        },
        itemMargin: {
          horizontal: 15,
          vertical: 5
        },
        fontSize: '13px',
        fontFamily: 'Public Sans',
        fontWeight: 400,
        labels: {
          colors: headingColor,
          useSeriesColors: false
        }
      },
      tooltip: {
        theme: false
      },
      grid: {
        padding: {
          top: 15
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '75%',
            labels: {
              show: true,
              value: {
                fontSize: '26px',
                fontFamily: 'Public Sans',
                color: headingColor,
                fontWeight: 500,
                offsetY: -30,
                formatter: function (val) {
                  return parseInt(val);
                }
              },
              name: {
                offsetY: 20,
                fontFamily: 'Public Sans'
              },
              total: {
                show: true,
                fontSize: '0.9rem',
                label: 'Total',
                color: labelColor,
                formatter: function (w) {
                  return contractsByStatus.total;
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 1025,
          options: {
            chart: {
              height: 380
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 300
            }
          }
        }
      ]
    };
  if (typeof contractsByStatusEl !== undefined && contractsByStatusEl !== null) {
    const contractsByStatus = new ApexCharts(contractsByStatusEl, contractsByStatusConfig);
    contractsByStatus.render();
  }

  // contracts by contractsByDistribution
  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }
  //   const chartColors = {
  //   donut: {
  //     series1: config.colors.warning,
  //     series2: config.colors.danger,
  //     series3: config.colors.success,
  //     series4: config.colors.secondary,
  //     series5: config.colors.info,
  //     series6: '#3a622d',
  //   }
  // };
  // Generated Leads Chart
  // --------------------------------------------------------------------
  const contractsByDistributionEl = document.querySelector('#contractsByDistribution'),
    contractsByDistributionConfig = {
      chart: {
        height: 400,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: contractsByDistribution.map(item => item.assignable_type.split('\\')[2]),
      series: contractsByDistribution.map(item => item.contract_count),
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series2,
        chartColors.donut.series3,
        chartColors.donut.series4,
        chartColors.donut.series5,
        chartColors.donut.series6
      ],
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: false,
        formatter: function (val, opt) {
          return parseInt(val);
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        offsetY: 10,
        markers: {
          width: 8,
          height: 8,
          offsetX: -3
        },
        itemMargin: {
          horizontal: 15,
          vertical: 5
        },
        fontSize: '13px',
        fontFamily: 'Public Sans',
        fontWeight: 400,
        labels: {
          colors: headingColor,
          useSeriesColors: false
        }
      },
      tooltip: {
        theme: false
      },
      grid: {
        padding: {
          top: 15
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '75%',
            labels: {
              show: true,
              value: {
                fontSize: '26px',
                fontFamily: 'Public Sans',
                color: headingColor,
                fontWeight: 500,
                offsetY: -30,
                formatter: function (val) {
                  return parseInt(val);
                }
              },
              name: {
                offsetY: 20,
                fontFamily: 'Public Sans'
              },
              total: {
                show: true,
                fontSize: '0.9rem',
                label: 'Total',
                color: labelColor,
                formatter: function (w) {
                  return contractsByDistribution.map(item => item.contract_count).reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 1025,
          options: {
            chart: {
              height: 380
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 300
            }
          }
        }
      ]
    };
  if (typeof contractsByDistributionEl !== undefined && contractsByDistributionEl !== null) {
    const contractsByDistribution = new ApexCharts(contractsByDistributionEl, contractsByDistributionConfig);
    contractsByDistribution.render();
  }
})();
