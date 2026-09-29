<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/transactions.css') ?>">

<?php $this->load->view('components/transaction_filters'); ?>
<?php $this->load->view('components/transaction_toolbar'); ?>
<?php $this->load->view('components/transaction_list'); ?>
<?php $this->load->view('components/deleted_transactions_modal'); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<script src="<?= base_url('assets/js/transactions.js') ?>"></script>

<script>
    var currentPage = 1;
    var TRANSACTION_API = "<?= site_url('api/transaction') ?>";
    var TRANSACTION_DELETED_API = "<?= site_url('api/transaction/deleted') ?>";
    var TRANSACTION_RESTORE_API = "<?= site_url('api/transaction/restore') ?>";
    var CATEGORIES_API = "<?= site_url('api/categories') ?>";

    function fillCategorySelect(select, categories, emptyLabel) {
        if (!select) {
            return;
        }

        select.innerHTML = '<option value="">' + emptyLabel + '</option>';

        categories.forEach(function (category) {
            var option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.title;
            select.appendChild(option);
        });
    }

    function loadCategories() {
        fetch(CATEGORIES_API)
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data.status) {
                    return;
                }

                fillCategorySelect(
                    document.getElementById('filterCategory'),
                    data.categories,
                    'همه دسته‌ها'
                );

                fillCategorySelect(
                    document.getElementById('tx_category'),
                    data.categories,
                    'بدون دسته'
                );

                if (typeof renderCategories === 'function') {
                    renderCategories(data.categories);
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    function renderPagination(pagination) {

        var el = document.getElementById('pagination');

        var totalPages = Number(pagination.total_pages);
        var current = Number(pagination.current_page);

        if (totalPages <= 1) {
            el.innerHTML = '';
            return;
        }

        var html = '';

        html += `
            <button
            onclick="loadTransactions(1)"
            ${current === 1 ? 'disabled' : ''}>
                اولین
            </button>
        `;

        var start = Math.max(1, current - 2);
        var end = Math.min(totalPages, current + 2);

        for (let i = start; i <= end; i++) {

            html += `
                <button
                    class="${i === current ? 'active' : ''}"
                    onclick="loadTransactions(${i})">

                    ${formatNumber(i)}

                </button>
            `;
        }

        html += `
            <button
            onclick="loadTransactions(${totalPages})"
            ${current === totalPages ? 'disabled' : ''}>
                آخرین
            </button>
        `;


        el.innerHTML = html;
    }

    function loadTransactions(page) {
        currentPage = page || 1;

        var params = new URLSearchParams({
            page: currentPage,
            search: document.getElementById('search').value,
            type: document.getElementById('filterType').value,
            category_id: document.getElementById('filterCategory').value,
            from_date: convertPersianToGregorian(document.getElementById('filterFromDate').value),
            to_date: convertPersianToGregorian(document.getElementById('filterToDate').value),
            sort: document.getElementById('sort').value || 'newest'
        });

        fetch(TRANSACTION_API + '?' + params)
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                var list = document.getElementById('transactionList');
                var countEl = document.getElementById('transactionCount');

                if (!data.status || !data.transactions || !data.transactions.length) {
                    renderTransactionList(list, []);
                    document.getElementById('pagination').innerHTML = '';
                    countEl.textContent = 'تعداد: ' + formatNumber(0);
                    return;
                }

                var total = (data.pagination && data.pagination.total != null)
                    ? data.pagination.total
                    : data.transactions.length;

                countEl.textContent = 'تعداد: ' + formatNumber(total);
                renderTransactionList(list, data.transactions);
                bindTransactionListActions(list);
                renderPagination(data.pagination);
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    function loadDeletedTransactions() {
        var list = document.getElementById('deletedTransactionList');
        var countEl = document.getElementById('deletedTransactionCount');

        if (!list) {
            return;
        }

        fetch(TRANSACTION_DELETED_API)
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data.status) {
                    return;
                }

                var transactions = data.transactions || [];
                var countLabel = 'تعداد: ' + formatNumber(transactions.length);

                if (countEl) {
                    countEl.textContent = countLabel;
                }

                renderTransactionList(list, transactions, {
                    deleted: true,
                    emptyText: 'تراکنش حذف‌شده‌ای وجود ندارد'
                });
                bindTransactionListActions(list);
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    function restoreTransaction(id) {
        if (!id) {
            return;
        }

        fetch(TRANSACTION_RESTORE_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                id: id
            })
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message || 'Restore failed');
                    return;
                }

                loadTransactions(currentPage || 1);
                loadDeletedTransactions();

                if (typeof loadDashboardSummary === 'function') {
                    loadDashboardSummary();
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }

    function initPersianDatePickers() {
        var pickerOptions = {
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false,
            calendar: {
                persian: {
                    locale: 'fa'
                }
            }
        };

        $('#filterFromDate').pDatepicker(pickerOptions);
        $('#filterToDate').pDatepicker(pickerOptions);

        if ($('#tx_date_display').length) {
            $('#tx_date_display').pDatepicker($.extend({}, pickerOptions, {
                initialValue: true,
                onSelect: function () {
                    var shamsi = document.getElementById('tx_date_display').value;
                    document.getElementById('tx_date').value = convertPersianToGregorian(shamsi);
                }
            }));
        }
    }

    function setTransactionDateDisplay(gregorianDate) {
        var display = document.getElementById('tx_date_display');
        var hidden = document.getElementById('tx_date');
        if (!display || !hidden) {
            return;
        }

        var g = gregorianDate || (typeof todayGregorianDate === 'function'
            ? todayGregorianDate()
            : new Date().toISOString().split('T')[0]);
        hidden.value = g;
        display.value = formatPersianDate(g);
    }

    $(document).ready(function () {
        initPersianDatePickers();
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadCategories();
        loadTransactions(1);

        var deletedModal = document.getElementById('deletedTransactionsModal');
        if (deletedModal) {
            deletedModal.addEventListener('show.bs.modal', function () {
                loadDeletedTransactions();
            });
        }

        document.getElementById('sort').addEventListener('change', function () {
            loadTransactions(1);
        });

        document.getElementById('applyFiltersBtn').addEventListener('click', function () {
            loadTransactions(1);
        });

        document.getElementById('search').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadTransactions(1);
            }
        });
    });
</script>
