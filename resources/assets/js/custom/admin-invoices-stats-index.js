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

  // companies by projects
  const companiesByInvoicesEl = document.querySelector('#comapaniesByInvoicesChart');
  const companiesByInvoicesConfig = {
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
          companiesByInvoicesConfig.labels[opts.dataPointIndex] +
          ': (' +
          companiesByInvoices[opts.dataPointIndex].total +
          ')'
        );
      },
      offsetX: 0,
      dropShadow: {
        enabled: false
      }
    },
    labels: companiesByInvoices.map(item => item.name),
    series: [
      {
        data: companiesByInvoices.map(item => item.percentage)
      }
    ],

    xaxis: {
      categories: companiesByInvoices.map(item => item.name),
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
  if (typeof companiesByInvoicesEl !== undefined && companiesByInvoicesEl !== null) {
    const companiesByInvoicesChart = new ApexCharts(companiesByInvoicesEl, companiesByInvoicesConfig);
    companiesByInvoicesChart.render();
  }

  var topInvoicesByValueConfig = {
    tooltipLabels: topInvoicesByValue.map(function (item) {
      return item.subject;
    }),
    printable_values: topInvoicesByValue.map(function (item) {
      return item.amount;
    }),
    series: [
      {
        // name: 'Invoices By Value',
        data: topInvoicesByValue.map(function (item) {
          return item.amount;
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
        return val > 0 ? topInvoicesByValueConfig.printable_values[data.dataPointIndex] : '';
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
          '<span><b>Invoice:</b> ' +
          topInvoicesByValueConfig.tooltipLabels[seriesIndex] +
          '</span>' +
          '<span><b>Value:</b> ' +
          topInvoicesByValueConfig.printable_values[seriesIndex] +
          '</span>' +
          '</div>'
        );
      }
    },
    xaxis: {
      categories: topInvoicesByValue.map(function (item) {
        return item.subject;
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
          return val ? `${val}` : '';
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

  var chart = new ApexCharts(document.querySelector('#topInvoicesByValue'), topInvoicesByValueConfig);
  chart.render();


  // invoices by Due Date
  var invoicesByDueDateConfig = {
    series: [
      {
        // name: 'Invoices By Value',
        data: invoicesByDueDate.map(function (item) {
          return item.invoices_count;
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
      categories: invoicesByDueDate.map(function (item) {
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

  var chart = new ApexCharts(document.querySelector('#invoicesByDueDate'), invoicesByDueDateConfig);
  chart.render();

  // invoices by statuses
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
  const invoicesByStatusEl = document.querySelector('#invoicesByStatus'),
    invoicesByStatusConfig = {
      chart: {
        height: 400,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: Object.keys(invoicesByStatus).filter(key => key !== 'total'),
      series: Object.keys(invoicesByStatus)
        .filter(key => key !== 'total')
        .map(key => invoicesByStatus[key]),
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
                  return invoicesByStatus.total;
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
  if (typeof invoicesByStatusEl !== undefined && invoicesByStatusEl !== null) {
    const invoicesByStatus = new ApexCharts(invoicesByStatusEl, invoicesByStatusConfig);
    invoicesByStatus.render();
  }

  // invoices by invoicesByDistribution
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
  const invoicesByDistributionEl = document.querySelector('#invoicesByDistribution'),
    invoicesByDistributionConfig = {
      chart: {
        height: 400,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: invoicesByDistribution.map(item => item.type),
      series: invoicesByDistribution.map(item => item.invoice_count),
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
                  return invoicesByDistribution.map(item => item.invoice_count).reduce((a, b) => a + b, 0);
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
  if (typeof invoicesByDistributionEl !== undefined && invoicesByDistributionEl !== null) {
    const invoicesByDistribution = new ApexCharts(invoicesByDistributionEl, invoicesByDistributionConfig);
    invoicesByDistribution.render();
  }
})();
