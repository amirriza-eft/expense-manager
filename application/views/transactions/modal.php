<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true" ref="transactionModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel text-white" style="background:#1e1e24; border:1px solid var(--border-subtle);">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="txModalTitle">{{ transactionForm.id ? 'ویرایش تراکنش' : 'ثبت تراکنش جدید' }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="txForm" @submit.prevent="saveTransaction">

                <div v-if="transactionMessage" class="alert py-2 px-3 small"
                     :class="transactionMessageSuccess ? 'alert-success' : 'alert-danger'">{{ transactionMessage }}</div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="tx_title" class="form-label small text-muted">عنوان تراکنش</label>
                        <input type="text" class="form-control" id="tx_title" required
                               placeholder="مثلاً: حقوق ماهانه، خرید سوپرمارکت" v-model="transactionForm.title">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_type" class="form-label small text-muted">نوع</label>
                            <select id="tx_type" class="form-select" required
                                    v-model="transactionForm.type" @change="transactionForm.category_id = ''">
                                <option value="expense">هزینه</option>
                                <option value="income">درآمد</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="tx_category" class="form-label small text-muted">دسته‌بندی</label>
                            <select id="tx_category" class="form-select" v-model="transactionForm.category_id">
                                <option value="">بدون دسته</option>
                                <template v-for="category in categoryOptions" :key="category.id">
                                    <option v-if="category.type === transactionForm.type" :value="category.id">{{ category.title }}</option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_amount" class="form-label small text-muted">مبلغ (تومان)</label>
                            <input type="text" class="form-control" id="tx_amount" required
                                   placeholder="مثال: ۵۰۰۰۰۰۰" inputmode="numeric"
                                   :value="transactionForm.amount" @input="onAmountInput">
                        </div>

                        <div class="col-6">
                            <label for="tx_date_display" class="form-label small text-muted">تاریخ تراکنش</label>
                            <input type="text" class="form-control" id="tx_date_display" required
                                   placeholder="تاریخ شمسی" autocomplete="off"
                                   ref="txDateDisplay" v-model="transactionForm.date_display">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="tx_description" class="form-label small text-muted">توضیحات (اختیاری)</label>
                        <textarea class="form-control" id="tx_description" rows="2"
                                  placeholder="توضیحات تکمیلی پیرامون این تراکنش..."
                                  v-model="transactionForm.description"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-orange-glow" :disabled="transactionLoading">
                        {{ transactionLoading ? 'در حال ذخیره...' : 'ذخیره تراکنش' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
