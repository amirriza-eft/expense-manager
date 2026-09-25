<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold text-white mb-0">لیست تراکنش‌ها</h5>
        <span id="transactionCount" class="text-muted small"></span>
    </div>

    <div id="transactionList" class="transaction-list" aria-live="polite"></div>

    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="صفحه‌بندی تراکنش‌ها">
            <div id="pagination" class="pagination pagination-dark"></div>
        </nav>
    </div>

    <div class="d-flex justify-content-center mt-4 pt-3 border-top"
         style="border-color: var(--border-subtle) !important;">
        <button
            type="button"
            class="btn btn-orange-outline"
            id="openDeletedTransactionsBtn"
            data-bs-toggle="modal"
            data-bs-target="#deletedTransactionsModal"
        >
            <i class="bi bi-trash"></i>
            تراکنش‌های حذف‌شده
            <span id="deletedTransactionCountBadge" class="badge bg-secondary ms-1">۰</span>
        </button>
    </div>
</div>
