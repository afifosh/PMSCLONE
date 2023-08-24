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
          return contractsByTypeConfig.labels[opts.dataPointIndex] + ': ('+ contractsByType[opts.dataPointIndex].total +')';
        },
        offsetX: 0,
        dropShadow: {
          enabled: false
        }
      },
      labels: contractsByType.map((item) => item.name),
      series: [
        {
          data: contractsByType.map((item) => item.percentage)
        }
      ],

      xaxis: {
        categories: contractsByType.map((item) => item.name),
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
        return contractsByValueConfig.labels[opts.dataPointIndex] + ': ('+ contractsByValue[opts.dataPointIndex].total +')';
      },
      offsetX: 0,
      dropShadow: {
        enabled: false
      }
    },
    labels: contractsByValue.map((item) => item.name),
    series: [
      {
        data: contractsByValue.map((item) => item.percentage)
      }
    ],

    xaxis: {
      categories: contractsByValue.map((item) => item.name),
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
        return companiesByProjectsConfig.labels[opts.dataPointIndex] + ': ('+ companiesByProjects[opts.dataPointIndex].total +')';
      },
      offsetX: 0,
      dropShadow: {
        enabled: false
      }
    },
    labels: companiesByProjects.map((item) => item.name),
    series: [
      {
        data: companiesByProjects.map((item) => item.percentage)
      }
    ],

    xaxis: {
      categories: companiesByProjects.map((item) => item.name),
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
})();
