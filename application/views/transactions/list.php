<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold text-white mb-0">لیست تراکنش‌ها</h5>
        <span id="transactionCount" class="text-muted small">{{ totalCount === null ? '' : 'تعداد: ' + fmtNumber(totalCount) }}</span>
    </div>

    <div id="transactionList" class="transaction-list" aria-live="polite" ref="transactionList">
        <div v-if="!transactionLoading && !transactions.length" class="transaction-empty">
            <i class="bi bi-inbox"></i>تراکنشی وجود ندارد
        </div>

        <article v-for="tx in transactions" :key="tx.id" class="transaction-card" :data-transaction-id="tx.id">
            <div class="transaction-card__left">
                <div class="transaction-card__actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" title="ویرایش"
                            @click="openEditTransactionModal(tx)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="حذف"
                            @click="confirmDeleteTransaction(tx.id)">
                        <i class="bi bi-trash"></i>
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
                    <div class="transaction-card__icon" :class="'transaction-card__icon--' + typeClass(tx.type)" aria-hidden="true">
                        <i class="bi" :class="typeIcon(tx.type)"></i>
                    </div>

                    <div class="transaction-card__info">
                        <h6 class="transaction-card__title">{{ tx.title }}</h6>

                        <div class="transaction-card__meta">
                            <span class="badge" :class="tx.type === 'income' ? 'badge-income' : 'badge-expense'">{{ tx.type === 'income' ? 'درآمد' : 'هزینه' }}</span>
                            <span class="badge bg-secondary">{{ tx.category_name || 'بدون دسته' }}</span>
                        </div>

                        <div class="transaction-card__date">
                            <i class="bi bi-calendar3 ms-1"></i>
                            {{ fmtDate(tx.transaction_date) }}
                        </div>
                    </div>
                </div>

                <div class="transaction-card__amount" :class="'transaction-card__amount--' + typeClass(tx.type)" v-html="amountHtml(tx)"></div>
            </div>
        </article>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="صفحه‌بندی تراکنش‌ها">
            <div id="pagination" class="pagination">
                <template v-if="totalPages > 1">
                    <button @click="loadTransactions(1)" :disabled="currentPage === 1">اولین</button>
                    <button v-for="page in pageNumbers" :key="page" :class="{active: page === currentPage}"
                            @click="loadTransactions(page)">{{ fmtNumber(page) }}</button>
                    <button @click="loadTransactions(totalPages)" :disabled="currentPage === totalPages">آخرین</button>
                </template>
            </div>
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
            @click="loadDeletedTransactions"
        >
            <i class="bi bi-trash"></i>
            تراکنش‌های حذف‌شده
        </button>
    </div>
</div>
