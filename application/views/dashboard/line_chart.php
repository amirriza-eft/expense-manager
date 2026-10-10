<div id="finance-chart-app" class="chart-wrapper" v-cloak>

    <div class="chart-header d-flex justify-content-between align-items-center mt-5 mb-3 gap-3">
        <div>
            <h4 class="fw-bold text-white mb-1">نمودار مالی</h4>
            <p class="text-muted small mb-0">بررسی درآمد و هزینه‌های اخیر</p>
        </div>

        <select v-model="period" class="form-select form-select-sm flex-shrink-0">
            <option value="monthly">ماهانه</option>
            <option value="weekly">هفتگی</option>
        </select>
    </div>

    <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
        <div v-for="item in legend" :key="item.label" class="d-inline-flex align-items-center">
            <span class="legend-dot me-2 rounded-1" :style="{ background: item.color }"></span>
            <span class="small text-secondary">{{ item.label }}</span>
        </div>
    </div>

    <div v-if="loading" class="text-center text-muted small py-5">در حال بارگذاری...</div>
    <div v-if="error" class="alert alert-danger py-2 px-3 small border-0">{{ error }}</div>

    <div id="hs-curved-area-charts" ref="chartEl"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1"></script>

<script>
    Vue.createApp({
        data() {
            return {
                period: 'monthly',
                loading: false,
                error: '',

                legend: [
                    { label: 'درآمد',  color: '#198754' },
                    { label: 'هزینه',  color: '#dc3545' },
                    { label: 'موجودی', color: '#065b8a' }
                ],

                categories: [],
                incomes: [],
                expenses: [],
                balances: []
            };
        },

        computed: {
            series() {
                return [
                    { name: 'درآمد',  data: this.incomes },
                    { name: 'هزینه',  data: this.expenses },
                    { name: 'موجودی', data: this.balances }
                ];
            },

            chartOptions() {
                return {
                    chart: {
                        height: 300,
                        type: 'area',
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    series: this.series,
                    colors: this.legend.map(item => item.color),
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: { opacityFrom: 0.35, opacityTo: 0.05 }
                    },
                    grid: {
                        borderColor: '#dee2e6',
                        strokeDashArray: 3
                    },
                    xaxis: {
                        categories: this.categories,
                        tickAmount: 10,
                        labels: {
                            style: { colors: '#adb5bd', fontSize: '12px', fontWeight: 600 }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#adb5bd', fontSize: '13px', fontWeight: 700 },
                            formatter: this.formatAxisValue
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: value => value.toLocaleString('fa-IR') + ' تومان'
                        }
                    },
                    legend: { show: false },
                    responsive: [
                        {
                            breakpoint: 992,
                            options: { chart: { height: 280 } }
                        },
                        {
                            breakpoint: 576,
                            options: {
                                chart: { height: 240 },
                                grid: { padding: { left: 20, right: 20 } }
                            }
                        }
                    ]
                };
            }
        },

        watch: {
            period() {
                this.loadChart();
            }
        },

        mounted() {
            this.loadChart();
        },

        beforeUnmount() {
            if (this.controller) this.controller.abort();
            this.destroyChart();
        },

        methods: {
            formatAxisValue(value) {
                const abs  = Math.abs(value);
                const sign = value < 0 ? '-' : '';

                if (abs >= 1000000) {
                    return sign + (abs / 1000000).toLocaleString('fa-IR') + ' میلیون';
                }
                if (abs >= 1000) {
                    return sign + (abs / 1000).toLocaleString('fa-IR') + ' هزار';
                }
                return value.toLocaleString('fa-IR');
            },

            async loadChart() {
                if (this.controller) this.controller.abort();
                this.controller = new AbortController();

                this.loading = true;
                this.error = '';

                try {
                    const response = await axios.get("<?= site_url('api/chart') ?>", {
                        params: { period: this.period },
                        signal: this.controller.signal
                    });

                    const result = response.data;

                    if (!result.status) return;

                    const rows = result.data;

                    if (!rows || rows.length === 0) {
                        this.destroyChart();
                        return;
                    }

                    this.categories = rows.map(item => new Date(item.day).toLocaleDateString('fa-IR'));
                    this.incomes    = rows.map(item => Number(item.income)  || 0);
                    this.expenses   = rows.map(item => Number(item.expense) || 0);
                    this.balances   = rows.map(item => Number(item.balance) || 0);

                    this.renderChart();
                } catch (error) {
                    if (axios.isCancel(error)) return;
                    this.error = 'خطا در دریافت اطلاعات نمودار';
                } finally {
                    this.loading = false;
                }
            },

            renderChart() {
                this.destroyChart();

                this.chart = new ApexCharts(this.$refs.chartEl, this.chartOptions);
                this.chart.render();
            },

            destroyChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            }
        }
    }).mount('#finance-chart-app');
</script>

<style>
    [v-cloak] {
        display: none;
    }

    .chart-wrapper {
        width: 90%;
        max-width: 1295px;
        margin: 0 auto;
    }

    #hs-curved-area-charts {
        width: 90%;
        margin: 0 auto;
    }

    .chart-header {
        width: 100%;
    }

    .chart-header select {
        width: 120px;
    }

    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
    }

    @media (max-width: 992px) {
        .chart-wrapper {
            padding: 0 20px;
        }

        #hs-curved-area-charts {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .chart-wrapper {
            padding: 0 12px;
        }

        .chart-header {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .chart-header select {
            width: 100%;
        }

        #hs-curved-area-charts {
            width: 100%;
            margin-left: -5px;
            margin-right: -5px;
        }
    }
</style>