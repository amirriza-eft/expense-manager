<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row g-3 mb-5 justify-content-center">

    <?php $this->load->view('components/statistics_card', [
        'label' => 'موجودی کل',
        'icon' => 'bi-cash-stack brand-icon',
        'amount' => 0,
        'amount_id' => 'dashboardBudgetAmount',
        'prefix' => '',
        'amount_class' => 'text-white',
        'subtitle' => 'موجودی لحظه',
    ]); ?>

    <?php $this->load->view('components/statistics_card', [
        'label' => 'درآمد یک ماه گذشته',
        'icon' => 'bi-arrow-down-left-circle',
        'icon_style' => 'color: var(--income-green);',
        'amount' => 0,
        'amount_id' => 'dashboardMonthlyIncome',
        'prefix' => '+',
        'amount_class' => '',
        'amount_style' => 'color: var(--income-green);',
        'subtitle' => 'مجموع ورودی‌های این ماه',
    ]); ?>

    <?php $this->load->view('components/statistics_card', [
        'label' => 'هزینه یک ماه گذشته',
        'icon' => 'bi-arrow-up-right-circle',
        'icon_style' => 'color: var(--expense-red);',
        'amount' => 0,
        'amount_id' => 'dashboardMonthlyExpense',
        'prefix' => '-',
        'amount_class' => '',
        'amount_style' => 'color: var(--expense-red);',
        'subtitle' => 'مجموع خروجی‌های این ماه',
    ]); ?>

</div>

<script>
    const DASHBOARD_API = "<?= site_url('api/dashboard') ?>";

    function setDashboardAmount(el, prefix, amount) {
        if (!el) {
            return;
        }

        el.innerHTML =
            (prefix || '') + formatNumber(amount) +
            ' <span class="fs-6 text-muted">تومان</span>';
    }

    function loadDashboardSummary() {
        fetch(DASHBOARD_API)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.status) {
                    return;
                }

                setDashboardAmount(
                    document.getElementById('dashboardBudgetAmount'),
                    '',
                    data.budget_amount
                );
                setDashboardAmount(
                    document.getElementById('dashboardMonthlyIncome'),
                    '+',
                    data.monthly_income
                );
                setDashboardAmount(
                    document.getElementById('dashboardMonthlyExpense'),
                    '-',
                    data.monthly_expense
                );
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadDashboardSummary();
    });
</script>
