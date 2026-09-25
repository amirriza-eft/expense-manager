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

    <div class="transaction-list-actions d-flex flex-wrap justify-content-center gap-2 mt-4 pt-3 border-top"
         style="border-color: var(--border-subtle) !important;">
        <button
            type="button"
            class="btn btn-orange-outline"
            data-bs-toggle="modal"
            data-bs-target="#categoryManagerModal"
        >
            <i class="bi bi-tags"></i>
            مدیریت دسته‌ها
        </button>

        <button
            type="button"
            class="btn btn-orange-glow"
            data-bs-toggle="modal"
            data-bs-target="#transactionModal"
            onclick="openCreateTransactionModal()"
        >
            <i class="bi bi-plus-circle"></i>
            ثبت تراکنش
        </button>
    </div>
</div>
