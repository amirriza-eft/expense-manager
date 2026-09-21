<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Search & Filter Controls -->
<div class="glass-panel p-3 mb-4">
    <form method="GET" action="<?= site_url('home') ?>" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search" placeholder="جستجو در عنوان یا توضیحات..." value="<?= html_escape($this->input->get('search') ?? '') ?>">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="type" class="form-select">
                <option value="">نوع تراکنش (همه)</option>
                <option value="درآمد" <?= ($this->input->get('type') == 'درآمد') ? 'selected' : '' ?>>درآمد</option>
                <option value="هزینه" <?= ($this->input->get('type') == 'هزینه') ? 'selected' : '' ?>>هزینه</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select">
                <option value="">دسته‌بندی (همه)</option>
                <?php if(!empty($categories)): foreach($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($this->input->get('category_id') == $cat->id) ? 'selected' : '' ?>>
                        <?= html_escape($cat->name) ?> (<?= html_escape($cat->type) ?>)
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-orange-outline w-100">اعمال فیلتر</button>
            <a href="<?= site_url('home') ?>" class="btn btn-secondary px-3" title="پاکسازی فیلترها"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<!-- Transactions Table Component -->
<div class="glass-panel p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-white mb-0">لیست تراکنش‌ها</h5>
        <span class="text-muted small">تعداد: <?= !empty($transactions) ? count($transactions) : 0 ?></span>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
            <thead>
            <tr class="text-muted small border-bottom" style="border-color: var(--border-subtle) !important;">
                <th>عنوان</th>
                <th>نوع</th>
                <th>دسته‌بندی</th>
                <th>مبلغ (ریال)</th>
                <th>تاریخ تراکنش</th>
                <th>توضیحات</th>
                <th class="text-center">عملیات</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach($transactions as $tx): ?>
                    <tr style="border-color: var(--border-subtle);">
                        <td class="fw-bold text-white"><?= html_escape($tx->title) ?></td>
                        <td>
                            <?php if ($tx->type === 'درآمد'): ?>
                                <span class="badge badge-income">درآمد</span>
                            <?php else: ?>
                                <span class="badge badge-expense">هزینه</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= html_escape($tx->category_name ?? 'بدون دسته‌بندی') ?></span></td>
                        <td class="fw-bold <?= ($tx->type === 'درآمد') ? 'text-success' : 'text-danger' ?>">
                            <?= ($tx->type === 'درآمد' ? '+' : '-') . number_format($tx->amount) ?>
                        </td>
                        <td class="text-muted small"><?= html_escape($tx->transaction_date) ?></td>
                        <td class="text-muted small text-truncate" style="max-width: 180px;"><?= html_escape($tx->description ?? '-') ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" title="ویرایش" onclick='openEditTransactionModal(<?= json_encode($tx) ?>)'>
                                    <i class="bi bi-pencil text-light"></i>
                                </button>
                                <button class="btn btn-outline-danger" title="حذف" onclick="confirmDeleteTransaction(<?= $tx->id ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted small">هیچ تراکنشی ثبت نشده است. از دکمه «ثبت تراکنش جدید» استفاده کنید.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
