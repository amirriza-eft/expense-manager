<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row g-3 mb-5 justify-content-center">

    <?php $this->load->view('components/statistics_card', [
        'label' => 'مانده',
        'icon' => 'bi-cash-stack brand-icon',
        'amount' => $budget_amount ?? 0,
        'prefix' => '',
        'amount_class' => 'text-white',
        'subtitle' => 'موجودی لحظه',
    ]); ?>

    <?php $this->load->view('components/statistics_card', [
        'label' => 'درآمد ماه جاری',
        'icon' => 'bi-arrow-down-left-circle',
        'icon_style' => 'color: var(--income-green);',
        'amount' => $monthly_income ?? 0,
        'prefix' => '+',
        'amount_class' => '',
        'amount_style' => 'color: var(--income-green);',
        'subtitle' => 'مجموع ورودی‌های این ماه',
    ]); ?>

    <?php $this->load->view('components/statistics_card', [
        'label' => 'هزینه ماه جاری',
        'icon' => 'bi-arrow-up-right-circle',
        'icon_style' => 'color: var(--expense-red);',
        'amount' => $monthly_expense ?? 0,
        'prefix' => '-',
        'amount_class' => '',
        'amount_style' => 'color: var(--expense-red);',
        'subtitle' => 'مجموع خروجی‌های این ماه',
    ]); ?>

</div>
