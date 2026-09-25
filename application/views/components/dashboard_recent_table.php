<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/transactions.css') ?>">

<?php $this->load->view('components/transaction_filters'); ?>
<?php $this->load->view('components/transaction_toolbar'); ?>
<?php $this->load->view('components/transaction_list'); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<script src="<?= base_url('assets/js/transactions.js') ?>"></script>

<script>
    var currentPage = 1;
    var TRANSACTION_API = "<?= site_url('api/transaction') ?>";
    var CATEGORIES_API = "<?= site_url('api/categories') ?>";

    function convertPersianToGregorian(date) {
        if (!date) {
            return '';
        }

        var persianDigits = '۰۱۲۳۴۵۶۷۸۹';
        var englishDigits = '0123456789';

        date = date.replace(/[۰-۹]/g, function (char) {
            return englishDigits[persianDigits.indexOf(char)];
        });

        var parts = date.split('/');
        var pDate = new persianDate([
            Number(parts[0]),
            Number(parts[1]),
            Number(parts[2])
        ]);

        var result = pDate.toCalendar('gregorian').format('YYYY-MM-DD');

        return result.replace(/[۰-۹]/g, function (char) {
            return englishDigits[persianDigits.indexOf(char)];
        });
    }

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
            .then(function (res) { return res.json(); })
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
        var totalPages = Number(pagination && pagination.total_pages) || 0;
        var current = Number(pagination && pagination.current_page) || 1;
        var html = '';

        for (var i = 1; i <= totalPages; i++) {
            html +=
                '<button type="button" class="' + (i === current ? 'active' : '') + '" ' +
                'onclick="loadTransactions(' + i + ')">' +
                formatNumber(i) +
                '</button>';
        }

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
            .then(function (res) { return res.json(); })
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

    $(document).ready(function () {
        $('#filterFromDate').pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false
        });

        $('#filterToDate').pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadCategories();
        loadTransactions(1);

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
