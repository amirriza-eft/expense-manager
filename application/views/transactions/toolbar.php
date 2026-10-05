<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<br>
<br>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-4 mb-4">
    <div class="d-flex flex-wrap gap-2">
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

    <div class="d-flex align-items-center gap-2">
        <label for="sort" class="mb-0">مرتب‌سازی:</label>
        <select id="sort" class="form-select" style="width: 150px;">
            <option value="newest">جدیدترین</option>
            <option value="oldest">قدیمی‌ترین</option>
        </select>
    </div>
</div>
