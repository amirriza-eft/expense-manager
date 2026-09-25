<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 p-md-4 mt-4 deleted-transactions-section">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold text-white mb-1">تراکنش‌های حذف‌شده</h5>
            <p class="text-muted small mb-0">تراکنش‌های حذف‌شده را می‌توانید بازیابی کنید</p>
        </div>
        <span id="deletedTransactionCount" class="text-muted small"></span>
    </div>

    <div id="deletedTransactionList" class="transaction-list" aria-live="polite"></div>
</div>
