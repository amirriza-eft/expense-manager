<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="deletedTransactionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content glass-panel text-white"
             style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-1">تراکنش‌های حذف‌شده</h5>
                    <small class="text-muted">تراکنش‌های حذف‌شده را می‌توانید بازیابی کنید</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-3">
                    <span id="deletedTransactionCount" class="text-muted small"></span>
                </div>
                <div id="deletedTransactionList" class="transaction-list" aria-live="polite"></div>
            </div>
        </div>
    </div>
</div>
