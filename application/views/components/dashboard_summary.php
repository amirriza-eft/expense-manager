<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row g-3 mb-5 justify-content-center">

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">
                    موجودی کل
                </span>

                <i class="bi bi-cash-stack brand-icon fs-4"></i>
            </div>

            <h4
                    id="dashboardBudgetAmount"
                    class="fw-bold mb-1 text-white"
            >
                ۰
                <span class="fs-6 text-muted">تومان</span>
            </h4>

            <div class="small text-muted">
                موجودی لحظه
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">
                    درآمد یک ماه گذشته
                </span>

                <i
                        class="bi bi-arrow-down-left-circle fs-4"
                        style="color: var(--income-green);"
                ></i>
            </div>

            <h4
                    id="dashboardMonthlyIncome"
                    class="fw-bold mb-1"
                    style="color: var(--income-green);"
            >
                +۰
                <span class="fs-6 text-muted">تومان</span>
            </h4>

            <div class="small text-muted">
                مجموع ورودی‌های این ماه
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">
                    هزینه یک ماه گذشته
                </span>

                <i
                        class="bi bi-arrow-up-right-circle fs-4"
                        style="color: var(--expense-red);"
                ></i>
            </div>

            <h4
                    id="dashboardMonthlyExpense"
                    class="fw-bold mb-1"
                    style="color: var(--expense-red);"
            >
                -۰
                <span class="fs-6 text-muted">تومان</span>
            </h4>

            <div class="small text-muted">
                مجموع خروجی‌های این ماه
            </div>
        </div>
    </div>

</div>


<script>

    const DASHBOARD_API = "<?= site_url('api/dashboard') ?>";


    function formatDashboardAmount(amount) {

        amount = Number(amount) || 0;
        return amount.toLocaleString('fa-IR');
    }


    function setDashboardAmount(id, prefix, amount) {

        const element = document.getElementById(id);

        if (!element) {
            return;
        }

        element.innerHTML =
            prefix +
            formatDashboardAmount(amount) +
            ' <span class="fs-6 text-muted">تومان</span>';
    }


    function loadDashboardSummary() {

        fetch(DASHBOARD_API)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {

                if (!data.status) {
                    return;
                }

                setDashboardAmount(
                    'dashboardBudgetAmount',
                    '',
                    data.budget_amount
                );

                setDashboardAmount(
                    'dashboardMonthlyIncome',
                    '+',
                    data.monthly_income
                );

                setDashboardAmount(
                    'dashboardMonthlyExpense',
                    '-',
                    data.monthly_expense
                );

            })
            .catch(function (error) {
                console.error('Dashboard error:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadDashboardSummary();
    });

</script>