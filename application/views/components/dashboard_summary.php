<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- 4 Key Summary Metrics (Computed dynamically from transactions & budgets tables) -->
<div class="row g-3 mb-5 center">

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">مانده</span>
                <i class="bi bi-cash-stack brand-icon fs-4"></i>
            </div>
            <h4 class="fw-bold text-white mb-1"><?= number_format($budget_amount ?? 0) ?>
                <span class="fs-6 text-muted">تومان</span>
            </h4>
            <div class="small text-muted">موجودی لحظه</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">درآمد ماه جاری</span>
                <i class="bi bi-arrow-down-left-circle fs-4" style="color: var(--income-green);"></i>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--income-green);">+<?= number_format($monthly_income ?? 0) ?>
                <span class="fs-6 text-muted">تومان</span></h4>
            <div class="small text-muted">مجموع ورودی‌های این ماه</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">هزینه ماه جاری</span>
                <i class="bi bi-arrow-up-right-circle fs-4" style="color: var(--expense-red);"></i>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--expense-red);">-<?= number_format($monthly_expense ?? 0) ?>
                <span class="fs-6 text-muted">تومان</span></h4>
            <div class="small text-muted">مجموع خروجی‌های این ماه</div>
        </div>
    </div>

</div>