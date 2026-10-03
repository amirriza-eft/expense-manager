<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="categoryManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel text-white"
             style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">مدیریت دسته‌بندی‌های شما</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm" class="row g-2 align-items-end mb-4 p-3 rounded"
                      style="background: rgba(255,255,255,0.03);">

                    <input type="hidden" id="cat_id" value="">

                    <div class="col-12 col-md-5">
                        <label class="form-label small text-muted" for="cat_title">
                            نام دسته
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="cat_title"
                            placeholder="مثلاً: غذا، ورزش، حقوق"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small text-muted" for="cat_type">
                            نوع
                        </label>

                        <select class="form-select" id="cat_type" required>
                            <option value="expense">هزینه</option>
                            <option value="income">درآمد</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <button type="submit"
                                class="btn btn-orange-glow w-100"
                                id="catSubmitBtn">
                            افزودن دسته
                        </button>
                    </div>

                </form>

                <div class="mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-white mb-0">
                            دسته‌های فعال
                        </h6>

                        <span id="categoryCount" class="text-muted small">
                            تعداد: ۰
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="small fw-semibold mb-2"
                             style="color: var(--accent-orange);">
                            هزینه
                        </div>

                        <ul id="expenseCategoryList"
                            class="list-group list-group-flush p-0"></ul>
                    </div>

                    <div>
                        <div class="small fw-semibold mb-2"
                             style="color: var(--accent-orange);">
                            درآمد
                        </div>

                        <ul id="incomeCategoryList"
                            class="list-group list-group-flush p-0"></ul>
                    </div>

                </div>


                <div class="pt-3 border-top"
                     style="border-color: var(--border-subtle) !important;">

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-white mb-0">
                            دسته‌های حذف‌شده
                        </h6>

                        <span id="deletedCategoryCount" class="text-muted small">
                            تعداد: ۰
                        </span>
                    </div>

                    <div class="small text-muted mb-3">
                        دسته‌های حذف‌شده را می‌توانید بازیابی کنید
                    </div>

                    <div class="mb-3">
                        <div class="small fw-semibold mb-2"
                             style="color: var(--accent-orange);">
                            هزینه
                        </div>

                        <ul id="deletedExpenseCategoryList"
                            class="list-group list-group-flush p-0"></ul>
                    </div>

                    <div>
                        <div class="small fw-semibold mb-2"
                             style="color: var(--accent-orange);">
                            درآمد
                        </div>

                        <ul id="deletedIncomeCategoryList"
                            class="list-group list-group-flush p-0"></ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>