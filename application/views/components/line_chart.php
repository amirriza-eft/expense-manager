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
            درآمد
        </span>
    </div>


    <div class="d-inline-flex align-items-center">
        <span
            class="d-inline-block me-2 rounded-1"
            style="width:10px;height:10px;background-color:var(--chart-colors-chart-5-hex);">
        </span>

        <span class="small text-secondary">
            هزینه
        </span>
    </div>

</div>
<!-- End Legend Indicator -->

<div id="hs-curved-area-charts"></div>


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


                let categories = [];
                let incomes = [];
                let expenses = [];


                rows.forEach(function(item){

                    categories.push(
                        "روز " + Number(item.day).toLocaleString('fa-IR')
                    );


                    incomes.push(
                        Number(item.income)
                    );


                    expenses.push(
                        Number(item.expense)
                    );

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


                    xaxis:{


                        categories:categories,


                        labels:{

                            style:{
                                colors:'#6c757d',
                                fontSize:'13px'
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
                                fontSize:'13px'
                            },


                            formatter:function(value){

                                return value.toLocaleString('fa-IR');

                            }

                        }


                    },



                    tooltip:{


                        y:{


                            formatter:function(value){

                                return value.toLocaleString('fa-IR')
                                    + " تومان";

                            }

                        }


                    },


                    legend:{

                        show:false

                    }

                };



                let chart = new ApexCharts(
                    document.querySelector("#hs-curved-area-charts"),
                    options
                );


                chart.render();



            });


    });


</script>