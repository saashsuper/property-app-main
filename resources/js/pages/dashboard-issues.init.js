/**
 * Dashboard Issues Charts Initialization
 * Handles all issue-related charts on the dashboard
 */

// Chart variables
var issuesStatusChart = "";
var issuesPriorityChart = "";
var issuesTrendChart = "";
var issuesByBlockChart = "";

// Color helper function
function getChartColorsArray(chartId) {
    const chartElement = document.getElementById(chartId);
    if (chartElement) {
        const colors = chartElement.dataset.colors;
        if (colors) {
            const parsedColors = JSON.parse(colors);
            return parsedColors.map(function (value) {
                var newValue = value.replace(" ", "");
                if (newValue.indexOf(",") === -1) {
                    var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                    if (color) {
                        color = color.replace(" ", "");
                        return color;
                    } else return newValue;
                } else {
                    var val = value.split(',');
                    if (val.length == 2) {
                        var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                        rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                        return rgbaColor;
                    } else {
                        return newValue;
                    }
                }
            });
        } else {
            console.warn(`data-colors attribute not found on: ${chartId}`);
        }
    }
}

// Load all charts
function loadIssueCharts() {
    // Fetch data from API
    fetch('/api/dashboard/issues-stats')
        .then(response => response.json())
        .then(data => {
            // Issues by Status Chart
            var statusChartColors = getChartColorsArray("issues_status_chart");
            if (statusChartColors) {
                var statusOptions = {
                    series: [{
                        name: 'Issues',
                        data: [
                            data.by_status.Open,
                            data.by_status['In Progress'],
                            data.by_status.Resolved,
                            data.by_status.Closed,
                            data.by_status['On Hold']
                        ]
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: statusChartColors,
                    xaxis: {
                        categories: ['Open', 'In Progress', 'Resolved', 'Closed', 'On Hold'],
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Issues'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " issues"
                            }
                        }
                    }
                };

                if (issuesStatusChart != "")
                    issuesStatusChart.destroy();
                issuesStatusChart = new ApexCharts(document.querySelector("#issues_status_chart"), statusOptions);
                issuesStatusChart.render();
            }

            // Issues by Priority Chart
            var priorityChartColors = getChartColorsArray("issues_priority_chart");
            if (priorityChartColors) {
                var priorityOptions = {
                    series: [{
                        name: 'Issues',
                        data: [
                            data.by_priority.Low,
                            data.by_priority.Normal,
                            data.by_priority.High,
                            data.by_priority.Urgent,
                            data.by_priority.Critical
                        ]
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: priorityChartColors,
                    xaxis: {
                        categories: ['Low', 'Normal', 'High', 'Urgent', 'Critical'],
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Issues'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " issues"
                            }
                        }
                    }
                };

                if (issuesPriorityChart != "")
                    issuesPriorityChart.destroy();
                issuesPriorityChart = new ApexCharts(document.querySelector("#issues_priority_chart"), priorityOptions);
                issuesPriorityChart.render();
            }

            // Issues Trend Chart (Last 30 Days)
            var trendChartColors = getChartColorsArray("issues_trend_chart");
            if (trendChartColors) {
                var trendOptions = {
                    series: [{
                        name: 'Open',
                        data: data.trend.Open
                    }, {
                        name: 'In Progress',
                        data: data.trend['In Progress']
                    }, {
                        name: 'Resolved',
                        data: data.trend.Resolved
                    }, {
                        name: 'Closed',
                        data: data.trend.Closed
                    }],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    colors: trendChartColors,
                    xaxis: {
                        type: 'category',
                        categories: data.trend.dates
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Issues'
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                    },
                    legend: {
                        position: 'top'
                    }
                };

                if (issuesTrendChart != "")
                    issuesTrendChart.destroy();
                issuesTrendChart = new ApexCharts(document.querySelector("#issues_trend_chart"), trendOptions);
                issuesTrendChart.render();
            }

            // Issues by Block Chart
            var blockChartColors = getChartColorsArray("issues_by_block_chart");
            if (blockChartColors) {
                var blockNames = data.by_block.map(item => item.name);
                var blockCounts = data.by_block.map(item => item.count);

                var blockOptions = {
                    series: blockCounts,
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    labels: blockNames,
                    colors: blockChartColors,
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val, opts) {
                            return opts.w.globals.seriesTotals[opts.seriesIndex] + ' issues';
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " issues"
                            }
                        }
                    }
                };

                if (issuesByBlockChart != "")
                    issuesByBlockChart.destroy();
                issuesByBlockChart = new ApexCharts(document.querySelector("#issues_by_block_chart"), blockOptions);
                issuesByBlockChart.render();
            }
        })
        .catch(error => {
            console.error('Error loading issue charts:', error);
        });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    loadIssueCharts();
});

// Refresh charts on window resize
window.addEventListener('resize', function () {
    setTimeout(function () {
        loadIssueCharts();
    }, 100);
});

// Export functions for external use
window.dashboardIssues = {
    refreshCharts: loadIssueCharts,
    getStatusChart: function () { return issuesStatusChart; },
    getPriorityChart: function () { return issuesPriorityChart; },
    getTrendChart: function () { return issuesTrendChart; },
    getBlockChart: function () { return issuesByBlockChart; }
};
