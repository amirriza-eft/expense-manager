<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="deletedTransactionsModal" tabindex="-1" aria-hidden="true" ref="deletedModal">
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
                    <span id="deletedTransactionCount" class="text-muted small">{{ 'تعداد: ' + fmtNumber(deletedTransactions.length) }}</span>
                </div>

                <div id="deletedTransactionList" class="transaction-list" aria-live="polite" ref="deletedList">
                    <div v-if="!deletedTransactions.length" class="transaction-empty">
                        <i class="bi bi-inbox"></i>تراکنش حذف‌شده‌ای وجود ندارد
                    </div>

                    <article v-for="tx in deletedTransactions" :key="tx.id" class="transaction-card transaction-card--deleted" :data-transaction-id="tx.id">
                        <div class="transaction-card__left">
                            <div class="transaction-card__actions">
                                <button type="button" class="btn btn-sm btn-orange-outline" title="بازیابی"
                                        @click="restoreTransaction(tx.id)">
                                    <i class="bi bi-arrow-counterclockwise"></i> بازیابی
                                </button>
                            </div>
                        </div>

                        <div class="transaction-card__middle">
                            <div class="transaction-card__section-title">توضیحات</div>
                            <template v-if="(tx.description || '').trim()">
                                <p class="transaction-card__description" :class="{'is-expanded': expanded[tx.id]}">{{ tx.description.trim() }}</p>
                                <button type="button" class="transaction-card__toggle-desc"
                                        :class="{'is-visible': overflowing[tx.id]}" @click="toggleDescription(tx.id)">
                                    {{ expanded[tx.id] ? 'کمتر' : 'بیشتر' }}
                                </button>
                            </template>
                        </div>

                        <div class="transaction-card__right">
                            <div class="transaction-card__right-top">
                                <div class="transaction-card__icon" :class="'transaction-card__icon--' + tx.type" aria-hidden="true">
                                    <i class="bi bi-trash"></i>
                                </div>

                                <div class="transaction-card__info">
                                    <h6 class="transaction-card__title">{{ tx.title }}</h6>

                                    <div class="transaction-card__meta">
                                        <span class="badge" :class="tx.type === 'income' ? 'badge-income' : 'badge-expense'">{{ tx.type === 'income' ? 'درآمد' : 'هزینه' }}</span>
                                        <span class="badge bg-secondary">{{ tx.category_name || 'بدون دسته' }}</span>
                                        <span class="badge badge-deleted">حذف‌شده</span>
                                        <span v-if="tx.days_left !== undefined" class="badge badge-expiration">
                                            <i class="bi bi-clock-history ms-1"></i>
                                            حذف دائمی تا {{ tx.days_left }} روز دیگر
                                        </span>
                                    </div>

                                    <div class="transaction-card__date">
                                        <i class="bi bi-calendar3 ms-1"></i>
                                        {{ fmtDate(tx.transaction_date) }}
                                    </div>
                                </div>
                            </div>

                            <div class="transaction-card__amount" :class="'transaction-card__amount--' + tx.type" v-html="amountHtml(tx)"></div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
