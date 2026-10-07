<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="categoryManagerModal" tabindex="-1" aria-hidden="true" ref="categoryModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel text-white" style="background: #1e1e24; border: 1px solid var(--border-subtle);">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">مدیریت دسته‌بندی‌های شما</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div v-if="categoryMessage" class="alert py-2 px-3 small"
                     :class="categoryMessageSuccess ? 'alert-success' : 'alert-danger'">{{ categoryMessage }}</div>

                <form id="categoryForm" class="row g-2 align-items-end mb-4 p-3 rounded"
                      style="background: rgba(255,255,255,0.03);" @submit.prevent="saveCategory">

                    <div class="col-12 col-md-5">
                        <label class="form-label small text-muted" for="cat_title">نام دسته</label>
                        <input type="text" class="form-control" id="cat_title" required
                               placeholder="مثلاً: غذا، ورزش، حقوق" ref="categoryTitle" v-model="categoryForm.title">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small text-muted" for="cat_type">نوع</label>
                        <select class="form-select" id="cat_type" required v-model="categoryForm.type">
                            <option value="expense">هزینه</option>
                            <option value="income">درآمد</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-orange-glow w-100" id="catSubmitBtn" :disabled="categoryLoading">
                            {{ categoryLoading ? 'در حال ذخیره...' : (categoryForm.id ? 'به‌روزرسانی دسته' : 'افزودن دسته') }}
                        </button>
                    </div>

                </form>

                <div class="mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-white mb-0">دسته‌های فعال</h6>
                        <span id="categoryCount" class="text-muted small">{{ 'تعداد: ' + fmtNumber(categories.length) }}</span>
                    </div>

                    <div class="mb-3">
                        <div class="small fw-semibold mb-2" style="color: var(--accent-orange);">هزینه</div>

                        <ul id="expenseCategoryList" class="list-group list-group-flush p-0">
                            <li v-if="!expenseCategories.length" class="list-group-item bg-transparent text-muted text-center py-3">دسته هزینه‌ای وجود ندارد</li>
                            <li v-for="category in expenseCategories" :key="category.id"
                                class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 px-1">
                                <span class="small">{{ category.title }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-category-btn" title="ویرایش"
                                            @click="editCategory(category)"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn" title="حذف"
                                            :disabled="categoryLoading" @click="deleteCategory(category.id)"><i class="bi bi-trash"></i></button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <div class="small fw-semibold mb-2" style="color: var(--accent-orange);">درآمد</div>

                        <ul id="incomeCategoryList" class="list-group list-group-flush p-0">
                            <li v-if="!incomeCategories.length" class="list-group-item bg-transparent text-muted text-center py-3">دسته درآمدی وجود ندارد</li>
                            <li v-for="category in incomeCategories" :key="category.id"
                                class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 px-1">
                                <span class="small">{{ category.title }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-category-btn" title="ویرایش"
                                            @click="editCategory(category)"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn" title="حذف"
                                            :disabled="categoryLoading" @click="deleteCategory(category.id)"><i class="bi bi-trash"></i></button>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="pt-3 border-top" style="border-color: var(--border-subtle) !important;">

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-white mb-0">دسته‌های حذف‌شده</h6>
                        <span id="deletedCategoryCount" class="text-muted small">{{ 'تعداد: ' + fmtNumber(deletedCategories.length) }}</span>
                    </div>

                    <div class="small text-muted mb-3">دسته‌های حذف‌شده را می‌توانید بازیابی کنید</div>

                    <div class="mb-3">
                        <div class="small fw-semibold mb-2" style="color: var(--accent-orange);">هزینه</div>

                        <ul id="deletedExpenseCategoryList" class="list-group list-group-flush p-0">
                            <li v-if="!deletedExpenseCategories.length" class="list-group-item bg-transparent text-muted text-center py-3">دسته هزینه‌ای حذف‌شده‌ای وجود ندارد</li>
                            <li v-for="category in deletedExpenseCategories" :key="category.id"
                                class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 px-1 deleted-category-item">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="small text-truncate">{{ category.title }}</span>
                                    <span class="badge badge-deleted">حذف‌شده</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-orange-outline" title="بازیابی"
                                        :disabled="categoryLoading" @click="restoreCategory(category.id)">
                                    <i class="bi bi-arrow-counterclockwise"></i> بازیابی
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <div class="small fw-semibold mb-2" style="color: var(--accent-orange);">درآمد</div>

                        <ul id="deletedIncomeCategoryList" class="list-group list-group-flush p-0">
                            <li v-if="!deletedIncomeCategories.length" class="list-group-item bg-transparent text-muted text-center py-3">دسته درآمدی حذف‌شده‌ای وجود ندارد</li>
                            <li v-for="category in deletedIncomeCategories" :key="category.id"
                                class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 px-1 deleted-category-item">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="small text-truncate">{{ category.title }}</span>
                                    <span class="badge badge-deleted">حذف‌شده</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-orange-outline" title="بازیابی"
                                        :disabled="categoryLoading" @click="restoreCategory(category.id)">
                                    <i class="bi bi-arrow-counterclockwise"></i> بازیابی
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
