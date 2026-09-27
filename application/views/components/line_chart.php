<?php

?>


<style>

    .chart-wrapper {
        width:85%;
        max-width:900px;
        margin:auto;
    }


    #hs-curved-area-charts {
        width:100%;
    }


    @media(max-width:992px){

        .chart-wrapper{
            width:100%;
        }

    }


    @media(max-width:576px){

        .chart-wrapper{
            padding:0 15px;
        }

    }

</style>

<div class="chart-wrapper">

    <!-- Legend -->
    <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
        <div class="d-inline-flex align-items-center">
            <span class="me-2 rounded-1"
                  style="width:10px;height:10px;background:#198754;">
            </span>
            <span class="small text-secondary">
                درآمد
            </span>
        </div>


        <div class="d-inline-flex align-items-center">
            <span class="me-2 rounded-1"
                  style="width:10px;height:10px;background:#dc3545;">
            </span>
            <span class="small text-secondary">
                هزینه
            </span>
        </div>

    </div>


    <!-- Chart -->
    <div id="hs-curved-area-charts"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>

    window.addEventListener('load', function () {


        fetch("<?= site_url('api/chart') ?>")
            .then(response => response.json())
            .then(result => {


                if (!result.status) {
                    return;
                }


                let rows = result.data;

                if(!rows || rows.length === 0){
                    console.log("No chart data");
                    return;
                }

                let categories = [];
                let incomes = [];
                let expenses = [];


                rows.forEach(function(item, index){

                    let day = Number(item.day);

                    let date = new Date(
                        new Date().getFullYear(),
                        new Date().getMonth(),
                        day
                    );


                    let shamsi = date.toLocaleDateString('fa-IR');


                    categories.push(shamsi);

                    incomes.push(Number(item.income) || 0);

                    expenses.push(Number(item.expense) || 0);

                });



                let options = {

                    chart:{
                        height:300,
                        type:'area',
                        toolbar:{
                            show:false
                        },
                        zoom:{
                            enabled:false
                        }
                    },


                    series:[

                        {
                            name:'درآمد',
                            data: incomes
                        },

                        {
                            name:'هزینه',
                            data: expenses
                        }

                    ],

                    colors:[
                        '#198754',
                        '#dc3545'
                    ],

                    dataLabels:{
                        enabled:false
                    },

                    stroke:{
                        curve:'smooth',
                        width:3
                    },

                    fill:{
                        type:'gradient',
                        gradient:{
                            opacityFrom:0.35,
                            opacityTo:0.05
                        }
                    },

                    grid:{
                        borderColor:'#dee2e6',
                        strokeDashArray:3
                    },

                    xaxis: {

                        categories: categories,

                        tickAmount: 8,

                        labels: {

                            show:true,

                            style:{
                                colors:'#6c757d',
                                fontSize:'12px',
                                fontWeight:600
                            },


                            formatter:function(value, timestamp, opts){

                                if(opts && opts.tickAmount){

                                    return value;

                                }

                                return value;

                            }

                        },

                        axisBorder:{
                            show:false
                        },

                        axisTicks:{
                            show:false
                        }

                    },

                    yaxis:{
                        labels:{
                            style:{
                                colors:'#6c757d',
                                fontSize:'13px',
                                fontWeight:700
                            },

                            formatter:function(value){

                                if(value >= 1000000){
                                    return (value / 1000000)
                                        .toLocaleString('fa-IR') + ' میلیون';
                                }

                                if(value >= 1000){
                                    return (value / 1000)
                                        .toLocaleString('fa-IR') + ' هزار';
                                }

                                return value.toLocaleString('fa-IR');

                            }

                        }
                    },

                    tooltip:{
                        y:{
                            formatter:function(value){

                                return '<strong>' +
                                    value.toLocaleString('fa-IR') +
                                    '</strong> تومان';

                            }
                        }
                    },

                    legend:{
                        show:false
                    },

                    responsive:[
                        {
                            breakpoint:992,
                            options:{
                                chart:{
                                    height:280
                                },

                                grid:{
                                    padding:{
                                        left:10,
                                        right:10
                                    }
                                },

                                stroke:{
                                    width:2
                                }
                            }
                        },

                        {
                            breakpoint:576,
                            options:{
                                chart:{
                                    height:240
                                },

                                grid:{
                                    padding:{
                                        left:20,
                                        right:20
                                    }
                                },

                                yaxis:{
                                    labels:{
                                        style:{
                                            fontSize:'10px'
                                        }
                                    }
                                }
                            }
                        }
                    ]
                };

                let chart = new ApexCharts(
                    document.querySelector("#hs-curved-area-charts"),
                    options
                );

                chart.render();

            });

    });


</script>