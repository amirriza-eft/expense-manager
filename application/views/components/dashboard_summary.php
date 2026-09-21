<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- 4 Key Summary Metrics (Computed dynamically from transactions & budgets tables) -->
<div class="row g-3 mb-4">
    <!-- 1. Total Budget -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">بودجه</span>
                <i class="bi bi-piggy-bank text-warning fs-4"></i>
            </div>
            <h4 class="fw-bold text-white mb-1"><?= number_format($budget_amount ?? 0) ?> <span class="fs-6 text-muted">ریال</span></h4>
            <div class="small text-muted">بودجه هدف تعیین‌شده</div>
        </div>
    </div>

    <!-- 2. Current Month Total Income -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">درآمد ماه جاری</span>
                <i class="bi bi-arrow-down-left-circle fs-4" style="color: var(--income-green);"></i>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--income-green);">+<?= number_format($monthly_income ?? 0) ?> <span class="fs-6 text-muted">ریال</span></h4>
            <div class="small text-muted">مجموع ورودی‌های این ماه</div>
        </div>
    </div>

    <!-- 3. Current Month Total Expense -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">هزینه ماه جاری</span>
                <i class="bi bi-arrow-up-right-circle fs-4" style="color: var(--expense-red);"></i>
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--expense-red);">-<?= number_format($monthly_expense ?? 0) ?> <span class="fs-6 text-muted">ریال</span></h4>
            <div class="small text-muted">مجموع خروجی‌های این ماه</div>
        </div>
    </div>

    <!-- 4. Remaining Balance -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-panel p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-medium">مانده</span>
                <i class="bi bi-cash-stack brand-icon fs-4"></i>
            </div>
<!--            <h4 class="fw-bold text-white mb-1">--><?php //= number_format($remaining_amount ?? 0) ?><!-- <span class="fs-6 text-muted">ریال</span></h4>-->
<!--            <div class="small --><?php //= ($remaining_amount >= 0) ? 'text-success' : 'text-danger' ?><!--">-->
<!--                --><?php //= ($remaining_amount >= 0) ? 'تراز مالی مثبت' : 'کسری بودجه' ?>
<!--            </div>-->
        </div>
    </div>
</div>