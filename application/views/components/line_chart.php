<?php

?>

<!-- Legend Indicator -->
<div class="d-flex justify-content-center justify-content-sm-end align-items-center gap-3 mb-3 mb-sm-4">

    <div class="d-inline-flex align-items-center">
        <span
            class="d-inline-block me-2 rounded-1"
            style="width:10px;height:10px;background-color:var(--chart-colors-primary-hex);">
        </span>

        <span class="small text-secondary">
            Income
        </span>
    </div>


    <div class="d-inline-flex align-items-center">
        <span
            class="d-inline-block me-2 rounded-1"
            style="width:10px;height:10px;background-color:var(--chart-colors-chart-5-hex);">
        </span>

        <span class="small text-secondary">
            Outcome
        </span>
    </div>

</div>
<!-- End Legend Indicator -->

<div id="hs-curved-area-charts"></div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

<script>
    window.addEventListener('load', function () {

        var options = {
            chart: {
                height: 300,
                type: 'area',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },

            series: [
                {
                    name: 'Income',
                    data: [
                        18000,
                        51000,
                        60000,
                        38000,
                        88000,
                        50000,
                        40000,
                        52000,
                        88000,
                        80000,
                        60000,
                        70000
                    ]
                },
                {
                    name: 'Outcome',
                    data: [
                        27000,
                        38000,
                        60000,
                        77000,
                        40000,
                        50000,
                        49000,
                        29000,
                        42000,
                        27000,
                        42000,
                        50000
                    ]
                }
            ],

            colors: [
                '#198754',
                '#dc3545'
            ],

            dataLabels: {
                enabled: false
            },

            stroke: {
                curve: 'smooth',
                width: 2
            },

            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05
                }
            },

            grid: {
                borderColor: '#dee2e6',
                strokeDashArray: 3
            },

            legend: {
                show: false
            },

            xaxis: {
                categories: [
                    '25 January 2023',
                    '26 January 2023',
                    '27 January 2023',
                    '28 January 2023',
                    '29 January 2023',
                    '30 January 2023',
                    '31 January 2023',
                    '1 February 2023',
                    '2 February 2023',
                    '3 February 2023',
                    '4 February 2023',
                    '5 February 2023'
                ],

                labels: {
                    style: {
                        colors: '#6c757d',
                        fontSize: '13px'
                    },

                    formatter: function (value) {
                        if (!value) return '';

                        var parts = value.split(' ');

                        return parts[0] + ' ' + parts[1].substring(0, 3);
                    }
                },

                axisBorder: {
                    show: false
                },

                axisTicks: {
                    show: false
                }
            },


            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d',
                        fontSize: '13px'
                    },

                    formatter: function (value) {
                        return value >= 1000
                            ? (value / 1000) + 'k'
                            : value;
                    }
                }
            },


            tooltip: {
                y: {
                    formatter: function (value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },


            responsive: [
                {
                    breakpoint: 568,
                    options: {
                        chart: {
                            height: 250
                        },

                        xaxis: {
                            labels: {
                                style: {
                                    fontSize: '11px'
                                },

                                formatter: function (value) {
                                    return value.substring(0, 3);
                                }
                            }
                        }
                    }
                }
            ]
        };


        var chart = new ApexCharts(
            document.querySelector("#hs-curved-area-charts"),
            options
        );


        chart.render();

    });
</script>
