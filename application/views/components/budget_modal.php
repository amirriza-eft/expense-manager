<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Modal: Create / Edit Transaction -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel text-white"
             style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="txModalTitle">ثبت تراکنش جدید</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="txForm">
                <div id="txMessage" class="alert d-none py-2 px-3 small"></div>
                <input type="hidden" name="id" id="tx_id" value="">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tx_title" class="form-label small text-muted">عنوان تراکنش</label>
                        <input type="text" class="form-control" name="title" id="tx_title" required
                               placeholder="مثلاً: حقوق ماهانه، خرید سوپرمارکت">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_type" class="form-label small text-muted">نوع</label>
                            <select name="type" id="tx_type" class="form-select" required>
                                <option value="expense">هزینه</option>
                                <option value="income">درآمد</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="tx_category" class="form-label small text-muted">دسته‌بندی</label>
                            <select name="category_id" id="tx_category" class="form-select">
                                <option value="">بدون دسته</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_amount" class="form-label small text-muted">مبلغ (تومان)</label>
                            <input type="number" class="form-control" name="amount" id="tx_amount" required min="1"
                                   placeholder="مثال: ۵۰۰۰۰۰۰" inputmode="numeric">
                        </div>
                        <div class="col-6">
                            <label for="tx_date_display" class="form-label small text-muted">تاریخ تراکنش</label>
                            <input type="text" class="form-control" id="tx_date_display"
                                   placeholder="تاریخ شمسی" autocomplete="off" required>
                            <input type="hidden" name="transaction_date" id="tx_date" value="">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="tx_description" class="form-label small text-muted">توضیحات (اختیاری)</label>
                        <textarea class="form-control" name="description" id="tx_description" rows="2"
                                  placeholder="توضیحات تکمیلی پیرامون این تراکنش..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-orange-glow">ذخیره تراکنش</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Transaction Confirmation -->
<div class="modal fade" id="deleteTxConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-panel text-white"
             style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold mb-2">حذف تراکنش</h6>
                <p class="text-muted small">آیا از حذف این تراکنش از سوابق مالی اطمینان دارید؟</p>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteTxBtn">بله، حذف کن</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Category Manager -->
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
                    <div class="col-12 col-md-8">
                        <label class="form-label small text-muted" for="cat_title">نام دسته</label>
                        <input type="text" class="form-control" id="cat_title"
                               placeholder="مثلاً: غذا، ورزش، حقوق" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-orange-glow w-100" id="catSubmitBtn">
                            افزودن دسته
                        </button>
                    </div>
                </form>
                <ul id="categoryList" class="list-group list-group-flush p-0"></ul>
            </div>
        </div>
    </div>
</div>

<script>
    var deleteTransactionId = null;

    var categoryForm = document.getElementById('categoryForm');
    var categoryList = document.getElementById('categoryList');
    var categoryId = document.getElementById('cat_id');
    var categoryTitle = document.getElementById('cat_title');
    var categorySubmitBtn = document.getElementById('catSubmitBtn');

    function openCreateTransactionModal() {
        document.getElementById('txModalTitle').innerText = 'ثبت تراکنش جدید';
        document.getElementById('txForm').reset();
        document.getElementById('tx_id').value = '';
        document.getElementById('txMessage').classList.add('d-none');

        if (typeof setTransactionDateDisplay === 'function') {
            setTransactionDateDisplay(new Date().toISOString().split('T')[0]);
        } else {
            document.getElementById('tx_date').value = new Date().toISOString().split('T')[0];
        }
    }

    function openEditTransactionModal(tx) {
        document.getElementById('txModalTitle').innerText = 'ویرایش تراکنش';
        document.getElementById('tx_id').value = tx.id;
        document.getElementById('tx_title').value = tx.title;
        document.getElementById('tx_type').value = tx.type;
        document.getElementById('tx_category').value = tx.category_id ?? '';
        document.getElementById('tx_amount').value = tx.amount;
        document.getElementById('tx_description').value = tx.description || '';
        document.getElementById('txMessage').classList.add('d-none');

        if (typeof setTransactionDateDisplay === 'function') {
            setTransactionDateDisplay(tx.transaction_date);
        } else {
            document.getElementById('tx_date').value = tx.transaction_date;
        }

        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }

    function confirmDeleteTransaction(id) {
        deleteTransactionId = id;
        new bootstrap.Modal(document.getElementById('deleteTxConfirmModal')).show();
    }

    function deleteTransaction() {
        if (!deleteTransactionId) {
            return;
        }

        fetch("<?= site_url('api/transaction/delete') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id: deleteTransactionId })
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('deleteTxConfirmModal')).hide();
                deleteTransactionId = null;

                if (typeof loadTransactions === 'function') {
                    loadTransactions(currentPage || 1);
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }

    function resetCategoryForm() {
        categoryId.value = '';
        categoryTitle.value = '';
        categorySubmitBtn.textContent = 'افزودن دسته';
    }

    function editCategory(category) {
        categoryId.value = category.id;
        categoryTitle.value = category.title;
        categorySubmitBtn.textContent = 'به‌روزرسانی دسته';
        categoryTitle.focus();
    }

    function deleteCategory(id) {
        if (!confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')) {
            return;
        }

        fetch('/api/categories/delete/' + id, { method: 'POST' })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }
                if (typeof loadCategories === 'function') {
                    loadCategories();
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }

    function renderCategories(categories) {
        categoryList.innerHTML = '';

        categories.forEach(function (category) {
            var li = document.createElement('li');
            li.className = 'list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 px-1';

            var title = document.createElement('span');
            title.className = 'small';
            title.textContent = category.title;

            var actions = document.createElement('div');
            actions.className = 'btn-group btn-group-sm';
            actions.innerHTML =
                '<button type="button" class="btn btn-sm btn-outline-secondary edit-category-btn">' +
                    '<i class="bi bi-pencil"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger delete-category-btn">' +
                    '<i class="bi bi-trash"></i>' +
                '</button>';

            actions.querySelector('.edit-category-btn').addEventListener('click', function () {
                editCategory(category);
            });
            actions.querySelector('.delete-category-btn').addEventListener('click', function () {
                deleteCategory(category.id);
            });

            li.appendChild(title);
            li.appendChild(actions);
            categoryList.appendChild(li);
        });
    }

    document.getElementById('confirmDeleteTxBtn').addEventListener('click', deleteTransaction);

    document.getElementById('txForm').addEventListener('submit', function (e) {
        e.preventDefault();

        var form = this;
        var displayDate = document.getElementById('tx_date_display');
        var hiddenDate = document.getElementById('tx_date');

        if (displayDate && displayDate.value) {
            hiddenDate.value = convertPersianToGregorian(displayDate.value);
        }

        if (!hiddenDate.value) {
            var messageEarly = document.getElementById('txMessage');
            messageEarly.classList.remove('d-none');
            messageEarly.className = 'alert alert-danger py-2 px-3 small';
            messageEarly.textContent = 'لطفاً تاریخ تراکنش را انتخاب کنید';
            return;
        }

        var id = document.getElementById('tx_id').value;
        var url = id
            ? "<?= site_url('api/transaction/update/') ?>" + id
            : "<?= site_url('api/transaction/create') ?>";
        var message = document.getElementById('txMessage');
        var button = form.querySelector('button[type="submit"]');

        button.disabled = true;
        button.textContent = 'در حال ذخیره...';

        fetch(url, { method: 'POST', body: new FormData(form) })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                message.classList.remove('d-none');
                message.textContent = data.message;
                message.className = data.status
                    ? 'alert alert-success py-2 px-3 small'
                    : 'alert alert-danger py-2 px-3 small';

                if (data.status) {
                    setTimeout(function () { location.reload(); }, 700);
                }
            })
            .catch(function () {
                message.className = 'alert alert-danger py-2 px-3 small';
                message.textContent = 'خطا در ارتباط با سرور';
            })
            .finally(function () {
                button.disabled = false;
                button.textContent = 'ذخیره تراکنش';
            });
    });

    categoryForm.addEventListener('submit', function (event) {
        event.preventDefault();

        var id = categoryId.value;
        var title = categoryTitle.value.trim();
        if (!title) {
            return;
        }

        var url = id
            ? '/api/categories/update/' + id
            : '/api/categories/create';

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ title: title })
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }
                resetCategoryForm();
                if (typeof loadCategories === 'function') {
                    loadCategories();
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    });
</script>
