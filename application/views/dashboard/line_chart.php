<div id="finance-chart-app" class="chart-wrapper" v-cloak>

    <div class="chart-header d-flex justify-content-between align-items-center mt-5 mb-3 gap-3 flex-wrap">
        <div>
            <h4 class="fw-bold text-white mb-1">نمودار مالی</h4>
            <p class="text-muted small mb-0">بررسی درآمد و هزینه‌ها در ماه جاری یا بازه انتخابی</p>
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                @click="resetToCurrentMonth" title="بازگشت به ماه جاری شمسی">
            ماه جاری
        </button>
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

<script type="importmap">
    {
        "imports": {
            "apexcharts": "<?= base_url('assets/js/apexcharts/apexcharts.esm.js') ?>",
        "apexcharts/core": "<?= base_url('assets/js/apexcharts/core.esm.js') ?>"
    }
}
</script>

<script type="module">
    import ApexCharts from 'apexcharts';

    Vue.createApp({
        data() {
            return {
                from: '',
                to: '',
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
                    stroke: { curve: 'smooth', width: 3, connectNulls: false },
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
                            style: { colors: '#adb5bd', fontSize: '12px', fontWeight: 600 },
                            rotate: -45,
                            hideOverlappingLabels: true
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
                            formatter: value => {
                                if (value === null || value === undefined) {
                                    return '—';
                                }
                                return Number(value).toLocaleString('fa-IR') + ' تومان';
                            }
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

        mounted() {
            window.reloadFinanceChart = (from, to) => this.loadChart(from, to);
            this.loadChart();
        },

        beforeUnmount() {
            if (this.controller) this.controller.abort();
            this.destroyChart();
        },

        methods: {
            formatAxisValue(value) {
                if (value === null || value === undefined) {
                    return '';
                }

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

            resetToCurrentMonth() {
                if (typeof clearTransactionDateFilters === 'function') {
                    clearTransactionDateFilters();
                    return;
                }

                this.loadChart('', '');
            },

            async loadChart(from, to) {
                if (from !== undefined) {
                    this.from = from || '';
                }
                if (to !== undefined) {
                    this.to = to || '';
                }

                if (this.controller) this.controller.abort();
                this.controller = new AbortController();

                this.loading = true;
                this.error = '';

                try {
                    const params = {};

                    if (this.from && this.to) {
                        if (this.from > this.to) {
                            this.error = 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد';
                            this.destroyChart();
                            return;
                        }
                        params.from = this.from;
                        params.to = this.to;
                    }

                    const response = await axios.get("<?= site_url('api/chart') ?>", {
                        params,
                        signal: this.controller.signal
                    });

                    const result = response.data;

                    if (!result.status) {
                        this.error = result.message || 'خطا در دریافت اطلاعات نمودار';
                        this.destroyChart();
                        return;
                    }

                    const rows = result.data || [];

                    if (rows.length === 0) {
                        this.categories = [];
                        this.incomes = [];
                        this.expenses = [];
                        this.balances = [];
                        this.destroyChart();
                        return;
                    }

                    this.categories = rows.map(item => item.label || formatPersianDate(item.day));
                    this.incomes    = rows.map(item => item.income === null ? null : Number(item.income) || 0);
                    this.expenses   = rows.map(item => item.expense === null ? null : Number(item.expense) || 0);
                    this.balances   = rows.map(item => item.balance === null ? null : Number(item.balance) || 0);

                    this.renderChart();
                } catch (error) {
                    if (axios.isCancel(error) || error.name === 'CanceledError' || error.code === 'ERR_CANCELED') {
                        return;
                    }

                    const message = error.response && error.response.data && error.response.data.message;
                    this.error = message || 'خطا در دریافت اطلاعات نمودار';
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

        #hs-curved-area-charts {
            width: 100%;
            margin-left: -5px;
            margin-right: -5px;
        }
    }
</style>
