/**
 * Transaction list / card rendering (presentation only).
 */
(function (global) {
    'use strict';

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function typeLabel(type) {
        return type === 'income' ? 'درآمد' : 'هزینه';
    }

    function typeBadgeClass(type) {
        return type === 'income' ? 'badge-income' : 'badge-expense';
    }

    function typeIcon(type) {
        return type === 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
    }

    function buildTransactionCard(tx, options) {
        options = options || {};
        var isDeleted = !!options.deleted;
        var isIncome = tx.type === 'income';
        var typeClass = isIncome ? 'income' : 'expense';
        var category = tx.category_name || 'بدون دسته';
        var description = (tx.description || '').trim();
        var amountHtml = typeof formatAmount === 'function'
            ? formatAmount(tx.amount, tx.type)
            : ((isIncome ? '+' : '-') + Number(tx.amount).toLocaleString('fa-IR'));

        var payload = encodeURIComponent(JSON.stringify(tx));
        var cardClass = 'transaction-card' + (isDeleted ? ' transaction-card--deleted' : '');

        var actionsHtml = isDeleted
            ? (
                '<button type="button" class="btn btn-sm btn-orange-outline" ' +
                    'data-action="restore-transaction" data-transaction-id="' + escapeHtml(tx.id) + '" title="بازیابی">' +
                    '<i class="bi bi-arrow-counterclockwise"></i> بازیابی' +
                '</button>'
            )
            : (
                '<button type="button" class="btn btn-sm btn-outline-secondary" ' +
                    'data-action="edit-transaction" data-transaction="' + payload + '" title="ویرایش">' +
                    '<i class="bi bi-pencil"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" ' +
                    'data-action="delete-transaction" data-transaction-id="' + escapeHtml(tx.id) + '" title="حذف">' +
                    '<i class="bi bi-trash"></i>' +
                '</button>'
            );

        return '' +
            '<article class="' + cardClass + '" data-transaction-id="' + escapeHtml(tx.id) + '">' +
                '<div class="transaction-card__icon transaction-card__icon--' + typeClass + '" aria-hidden="true">' +
                    '<i class="bi ' + (isDeleted ? 'bi-trash' : typeIcon(tx.type)) + '"></i>' +
                '</div>' +
                '<div class="transaction-card__body">' +
                    '<h6 class="transaction-card__title">' + escapeHtml(tx.title) + '</h6>' +
                    '<div class="transaction-card__meta">' +
                        '<span class="badge ' + typeBadgeClass(tx.type) + '">' + typeLabel(tx.type) + '</span>' +
                        '<span class="badge bg-secondary">' + escapeHtml(category) + '</span>' +
                        (isDeleted ? '<span class="badge badge-deleted">حذف‌شده</span>' : '') +
                        '<span class="transaction-card__date">' +
                            '<i class="bi bi-calendar3 ms-1"></i>' +
                            escapeHtml(typeof formatPersianDate === 'function'
                                ? formatPersianDate(tx.transaction_date)
                                : (tx.transaction_date || '')) +
                        '</span>' +
                    '</div>' +
                    (description
                        ? '<p class="transaction-card__description">' + escapeHtml(description) + '</p>' +
                          '<button type="button" class="transaction-card__toggle-desc" data-action="toggle-description">بیشتر</button>'
                        : '') +
                '</div>' +
                '<div class="transaction-card__side">' +
                    '<div class="transaction-card__amount transaction-card__amount--' + typeClass + '">' +
                        amountHtml +
                    '</div>' +
                    '<div class="transaction-card__actions">' + actionsHtml + '</div>' +
                '</div>' +
            '</article>';
    }

    function bindDescriptionToggles(container) {
        container.querySelectorAll('.transaction-card').forEach(function (card) {
            var desc = card.querySelector('.transaction-card__description');
            var toggle = card.querySelector('[data-action="toggle-description"]');
            if (!desc || !toggle) {
                return;
            }

            if (desc.scrollHeight > desc.clientHeight + 2) {
                toggle.classList.add('is-visible');
            }

            toggle.addEventListener('click', function () {
                var expanded = desc.classList.toggle('is-expanded');
                toggle.textContent = expanded ? 'کمتر' : 'بیشتر';
            });
        });
    }

    function renderTransactionList(container, transactions, options) {
        options = options || {};

        if (!container) {
            return;
        }

        var emptyText = options.emptyText || 'تراکنشی وجود ندارد';

        if (!transactions || !transactions.length) {
            container.innerHTML =
                '<div class="transaction-empty">' +
                    '<i class="bi bi-inbox"></i>' +
                    emptyText +
                '</div>';
            return;
        }

        container.innerHTML = transactions.map(function (tx) {
            return buildTransactionCard(tx, options);
        }).join('');
        bindDescriptionToggles(container);
    }

    function bindTransactionListActions(container) {
        if (!container || container.dataset.actionsBound === '1') {
            return;
        }

        container.dataset.actionsBound = '1';

        container.addEventListener('click', function (event) {
            var editBtn = event.target.closest('[data-action="edit-transaction"]');
            if (editBtn) {
                try {
                    var tx = JSON.parse(decodeURIComponent(editBtn.getAttribute('data-transaction')));
                    if (typeof openEditTransactionModal === 'function') {
                        openEditTransactionModal(tx);
                    }
                } catch (error) {
                    console.error(error);
                }
                return;
            }

            var deleteBtn = event.target.closest('[data-action="delete-transaction"]');
            if (deleteBtn && typeof confirmDeleteTransaction === 'function') {
                confirmDeleteTransaction(Number(deleteBtn.getAttribute('data-transaction-id')));
                return;
            }

            var restoreBtn = event.target.closest('[data-action="restore-transaction"]');
            if (restoreBtn && typeof restoreTransaction === 'function') {
                restoreTransaction(Number(restoreBtn.getAttribute('data-transaction-id')));
            }
        });
    }

    global.buildTransactionCard = buildTransactionCard;
    global.renderTransactionList = renderTransactionList;
    global.bindTransactionListActions = bindTransactionListActions;
    global.escapeHtml = escapeHtml;
})(window);
