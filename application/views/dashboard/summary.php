<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="dashboard-summary">
    <div class="row g-3 mb-5 justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-panel p-3 h-100 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">موجودی کل</span>
                    <i class="bi bi-cash-stack brand-icon fs-4"></i>
                </div>

                <h4 class="fw-bold mb-1 text-white">
                    {{ formatAmount(budgetAmount) }}
                    <span class="fs-6 text-muted">تومان</span>
                </h4>

                <div class="small text-muted">موجودی لحظه</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-panel p-3 h-100 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">درآمد یک ماه گذشته</span>
                    <i class="bi bi-arrow-down-left-circle fs-4"
                       style="color: var(--income-green);"></i>
                </div>

                <h4 class="fw-bold mb-1" style="color: var(--income-green);">
                    +{{ formatAmount(monthlyIncome) }}
                    <span class="fs-6 text-muted">تومان</span>
                </h4>

                <div class="small text-muted">مجموع ورودی‌های این ماه</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="glass-panel p-3 h-100 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">هزینه یک ماه گذشته</span>
                    <i class="bi bi-arrow-up-right-circle fs-4"
                       style="color: var(--expense-red);"></i>
                </div>

                <h4 class="fw-bold mb-1" style="color: var(--expense-red);">
                    -{{ formatAmount(monthlyExpense) }}
                    <span class="fs-6 text-muted">تومان</span>
                </h4>

                <div class="small text-muted">مجموع خروجی‌های این ماه</div>
            </div>
        </div>
    </div>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                budgetAmount: 0,
                monthlyIncome: 0,
                monthlyExpense: 0
            };
        },
        mounted() {
            this.loadSummary();
        },
        methods: {
            formatAmount(amount) {
                return Number(amount || 0).toLocaleString('fa-IR');
            },
            async loadSummary() {
                try {
                    const { data } = await axios.get("<?= site_url('api/dashboard') ?>");

                    if (!data.status) {
                        return;
                    }

                    this.budgetAmount = data.budget_amount;
                    this.monthlyIncome = data.monthly_income;
                    this.monthlyExpense = data.monthly_expense;
                } catch (error) {
                    console.error('Dashboard error:', error);
                }
            }
        }
    }).mount('#dashboard-summary');
</script>
