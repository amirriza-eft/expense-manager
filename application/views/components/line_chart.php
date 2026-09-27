<?php

?>


<style>
    .chart-wrapper {
        width:90%;
        max-width:1295px;
        margin:0 auto;
    }

    #hs-curved-area-charts {
        width:90%;
        margin:0 auto;

    }

    .chart-header {
        width:100%;
    }

    .chart-header select {
        width:120px;
    }

    @media(max-width:992px){
        .chart-wrapper{
            padding:0 20px;
        }

        #hs-curved-area-charts {
            width:100%;
        }
    }

    @media(max-width:576px){
        .chart-wrapper{
            padding:0 12px;
        }
        .chart-header{
            flex-direction:column;
            align-items:stretch !important;
            gap:12px;
        }
        .chart-header select{
            width:100%;
        }
        #hs-curved-area-charts{
            width:100%;
            margin-left:-5px;
            margin-right:-5px;
        }
    }

</style>

<div class="chart-wrapper">


    <!-- Chart Header -->
    <div class="chart-header d-flex justify-content-between align-items-center mt-5 mb-3 gap-3">        <div>
            <h4 class="fw-bold text-white mb-1">
                نمودار مالی
            </h4>

            <p class="text-muted small mb-0">
                بررسی درآمد و هزینه‌های اخیر
            </p>
        </div>


        <select id="chart-period"
                class="form-select form-select-sm flex-shrink-0">

            <option value="monthly">
                ماهانه
            </option>

            <option value="weekly">
                هفتگی
            </option>

        </select>

    </div>


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

        let chart = null;


        function loadChart(){

            let period = document.getElementById('chart-period').value;


            fetch("<?= site_url('api/chart') ?>?period=" + period)

                .then(response => response.json())

                .then(result => {


                    if(!result.status){
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



                    rows.forEach(function(item){


                        let date = new Date(item.day);


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
                                data:incomes
                            },

                            {
                                name:'هزینه',
                                data:expenses
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


                        xaxis:{

                            categories:categories,

                            tickAmount:10,

                            labels:{
                                style:{
                                    colors:'#adb5bd',
                                    fontSize:'12px',
                                    fontWeight:600
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
                                    colors:'#adb5bd',
                                    fontSize:'13px',
                                    fontWeight:700
                                },


                                formatter:function(value){

                                    if(value >= 1000000){

                                        return (
                                                value / 1000000
                                            ).toLocaleString('fa-IR')
                                            + ' میلیون';

                                    }


                                    if(value >= 1000){

                                        return (
                                                value / 1000
                                            ).toLocaleString('fa-IR')
                                            + ' هزار';

                                    }


                                    return value.toLocaleString('fa-IR');

                                }

                            }

                        },


                        tooltip:{

                            y:{

                                formatter:function(value){

                                    return value.toLocaleString('fa-IR')
                                        + ' تومان';

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

                                    }

                                }

                            }

                        ]

                    };



                    if(chart){

                        chart.destroy();

                    }



                    chart = new ApexCharts(
                        document.querySelector("#hs-curved-area-charts"),
                        options
                    );


                    chart.render();



                });


        }



        // first load

        loadChart();



        // change monthly / weekly

        document
            .getElementById('chart-period')
            .addEventListener('change', function(){

                loadChart();

            });


    });

</script>