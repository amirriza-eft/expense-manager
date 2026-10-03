<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

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
                            <input type="text"
                                   class="form-control"
                                   name="amount"
                                   id="tx_amount"
                                   required
                                   placeholder="مثال: ۵۰۰۰۰۰۰"
                                   inputmode="numeric">
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

<script>
    let deleteTransactionId = null;

    let categoryForm = document.getElementById('categoryForm');
    let categoryType = document.getElementById('cat_type');

    let expenseCategoryList = document.getElementById('expenseCategoryList');
    let incomeCategoryList = document.getElementById('incomeCategoryList');

    let deletedExpenseCategoryList =
        document.getElementById('deletedExpenseCategoryList');

    let deletedIncomeCategoryList =
        document.getElementById('deletedIncomeCategoryList');

    let categoryId = document.getElementById('cat_id');
    let categoryTitle = document.getElementById('cat_title');
    let categorySubmitBtn = document.getElementById('catSubmitBtn');
    let CATEGORIES_DELETED_API = "<?= site_url('api/categories/deleted') ?>";

    function openCreateTransactionModal() {
        document.getElementById('txModalTitle').innerText = 'ثبت تراکنش جدید';

        document.getElementById('txForm').reset();
        document.getElementById('tx_id').value = '';
        document.getElementById('txMessage').classList.add('d-none');

        fillCategorySelect(
            document.getElementById('tx_category'),
            allCategories,
            'بدون دسته',
            document.getElementById('tx_type').value
        );

        if (typeof setTransactionDateDisplay === 'function') {
            setTransactionDateDisplay(
                typeof todayGregorianDate === 'function'
                    ? todayGregorianDate()
                    : ''
            );
        } else {
            document.getElementById('tx_date').value =
                typeof todayGregorianDate === 'function'
                    ? todayGregorianDate()
                    : new Date().toISOString().split('T')[0];
        }
    }

    function openEditTransactionModal(tx) {
        document.getElementById('txModalTitle').innerText = 'ویرایش تراکنش';

        document.getElementById('tx_id').value = tx.id;
        document.getElementById('tx_title').value = tx.title;

        document.getElementById('tx_type').value = tx.type;

        fillCategorySelect(
            document.getElementById('tx_category'),
            allCategories,
            'بدون دسته',
            tx.type,
            tx.category_id
        );

        document.getElementById('tx_amount').value =
            formatAmountInput(tx.amount);

        document.getElementById('tx_description').value =
            tx.description || '';

        document.getElementById('txMessage').classList.add('d-none');

        if (typeof setTransactionDateDisplay === 'function') {
            setTransactionDateDisplay(tx.transaction_date);
        } else {
            document.getElementById('tx_date').value = tx.transaction_date;
        }

        bootstrap.Modal
            .getOrCreateInstance(
                document.getElementById('transactionModal')
            )
            .show();
    }

    function confirmDeleteTransaction(id) {
        deleteTransactionId = id;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteTxConfirmModal')).show();
    }

    function deleteTransaction() {
        if (!deleteTransactionId) {
            return;
        }

        let confirmBtn = document.getElementById('confirmDeleteTxBtn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
        }

        fetch("<?= site_url('api/transaction/delete/') ?>" + deleteTransactionId, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({id: deleteTransactionId})
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }

                let deleteModal = bootstrap.Modal.getInstance(
                    document.getElementById('deleteTxConfirmModal')
                );
                if (deleteModal) {
                    deleteModal.hide();
                }
                deleteTransactionId = null;

                if (typeof loadTransactions === 'function') {
                    loadTransactions(currentPage || 1);
                }
                if (typeof loadDeletedTransactions === 'function') {
                    loadDeletedTransactions();
                }
                if (typeof loadDashboardSummary === 'function') {
                    loadDashboardSummary();
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            })
            .finally(function () {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                }
            });
    }

    function resetCategoryForm() {
        categoryId.value = '';
        categoryTitle.value = '';
        categoryType.value = 'expense';
        categorySubmitBtn.textContent = 'افزودن دسته';
    }

    function editCategory(category) {
        categoryId.value = category.id;
        categoryTitle.value = category.title;
        categoryType.value = category.type;
        categorySubmitBtn.textContent = 'به‌روزرسانی دسته';
        categoryTitle.focus();
    }

    function deleteCategory(id) {
        if (!confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')) {
            return;
        }

        fetch("<?= site_url('api/categories/delete/') ?>" + id, {method: 'POST'})
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }
                if (typeof loadCategories === 'function') {
                    loadCategories();
                }

                loadDeletedCategories();
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }

    function restoreCategory(id) {
        fetch("<?= site_url('api/categories/restore/') ?>" + id, {method: 'POST'})
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.status) {
                    alert(data.message);
                    return;
                }
                if (typeof loadCategories === 'function') {
                    loadCategories();
                }

                loadDeletedCategories();
            })
            .catch(function (error) {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }

    function renderCategories(categories) {
        let expenseCategories = (categories || []).filter(function (category) {
            return category.type === 'expense';
        });

        let incomeCategories = (categories || []).filter(function (category) {
            return category.type === 'income';
        });

        renderActiveCategoryList(
            expenseCategoryList,
            expenseCategories,
            'دسته هزینه‌ای وجود ندارد'
        );

        renderActiveCategoryList(
            incomeCategoryList,
            incomeCategories,
            'دسته درآمدی وجود ندارد'
        );

        let countEl = document.getElementById('categoryCount');

        if (countEl) {
            countEl.textContent =
                'تعداد: ' + formatNumber((categories || []).length);
        }
    }


    function renderActiveCategoryList(list, categories, emptyText) {
        if (!list) {
            return;
        }

        list.innerHTML = '';

        if (!categories.length) {
            list.innerHTML =
                '<li class="list-group-item bg-transparent text-muted text-center py-3">' +
                emptyText +
                '</li>';

            return;
        }

        categories.forEach(function (category) {
            let li = document.createElement('li');

            li.className =
                'list-group-item bg-transparent text-white ' +
                'd-flex justify-content-between align-items-center py-2 px-1';

            let title = document.createElement('span');

            title.className = 'small';
            title.textContent = category.title;

            let actions = document.createElement('div');

            actions.className = 'btn-group btn-group-sm';

            actions.innerHTML =
                '<button type="button" ' +
                'class="btn btn-sm btn-outline-secondary edit-category-btn" ' +
                'title="ویرایش">' +
                '<i class="bi bi-pencil"></i>' +
                '</button>' +

                '<button type="button" ' +
                'class="btn btn-sm btn-outline-danger delete-category-btn" ' +
                'title="حذف">' +
                '<i class="bi bi-trash"></i>' +
                '</button>';

            actions
                .querySelector('.edit-category-btn')
                .addEventListener('click', function () {
                    editCategory(category);
                });

            actions
                .querySelector('.delete-category-btn')
                .addEventListener('click', function () {
                    deleteCategory(category.id);
                });

            li.appendChild(title);
            li.appendChild(actions);

            list.appendChild(li);
        });
    }


    function renderDeletedCategories(categories) {
        let expenseCategories = (categories || []).filter(function (category) {
            return category.type === 'expense';
        });

        let incomeCategories = (categories || []).filter(function (category) {
            return category.type === 'income';
        });

        renderDeletedCategoryList(
            deletedExpenseCategoryList,
            expenseCategories,
            'دسته هزینه‌ای حذف‌شده‌ای وجود ندارد'
        );

        renderDeletedCategoryList(
            deletedIncomeCategoryList,
            incomeCategories,
            'دسته درآمدی حذف‌شده‌ای وجود ندارد'
        );

        let countEl = document.getElementById('deletedCategoryCount');

        if (countEl) {
            countEl.textContent =
                'تعداد: ' + formatNumber((categories || []).length);
        }
    }


    function renderDeletedCategoryList(list, categories, emptyText) {
        if (!list) {
            return;
        }

        list.innerHTML = '';

        if (!categories.length) {
            list.innerHTML =
                '<li class="list-group-item bg-transparent text-muted text-center py-3">' +
                emptyText +
                '</li>';

            return;
        }

        categories.forEach(function (category) {
            let li = document.createElement('li');

            li.className =
                'list-group-item bg-transparent text-white ' +
                'd-flex justify-content-between align-items-center ' +
                'py-2 px-1 deleted-category-item';

            let titleWrap = document.createElement('div');

            titleWrap.className =
                'd-flex align-items-center gap-2 min-w-0';

            let title = document.createElement('span');

            title.className = 'small text-truncate';
            title.textContent = category.title;

            let badge = document.createElement('span');

            badge.className = 'badge badge-deleted';
            badge.textContent = 'حذف‌شده';

            titleWrap.appendChild(title);
            titleWrap.appendChild(badge);

            let restoreBtn = document.createElement('button');

            restoreBtn.type = 'button';
            restoreBtn.className = 'btn btn-sm btn-orange-outline';
            restoreBtn.title = 'بازیابی';

            restoreBtn.innerHTML =
                '<i class="bi bi-arrow-counterclockwise"></i> بازیابی';

            restoreBtn.addEventListener('click', function () {
                restoreCategory(category.id);
            });

            li.appendChild(titleWrap);
            li.appendChild(restoreBtn);

            list.appendChild(li);
        });
    }


    function loadDeletedCategories() {
        fetch(CATEGORIES_DELETED_API)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.status) {
                    return;
                }
                renderDeletedCategories(data.categories || []);
            })
            .catch(function (error) {
                console.error(error);
            });
    }

    document.getElementById('confirmDeleteTxBtn').addEventListener('click', deleteTransaction);

    let categoryManagerModal = document.getElementById('categoryManagerModal');

    if (categoryManagerModal) {

        categoryManagerModal.addEventListener('show.bs.modal', function () {
            loadDeletedCategories();
        });

        categoryManagerModal.addEventListener('hidden.bs.modal', resetCategoryForm);
    }

    let deleteTxConfirmModal = document.getElementById('deleteTxConfirmModal');
    if (deleteTxConfirmModal) {
        deleteTxConfirmModal.addEventListener('hidden.bs.modal', function () {
            deleteTransactionId = null;
        });
    }


    document.getElementById('txForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);

        // Convert Persian amount digits + remove commas
        formData.set(
            'amount',
            getAmountInputValue(document.getElementById('tx_amount').value)
        );

        let displayDate = document.getElementById('tx_date_display');
        let hiddenDate = document.getElementById('tx_date');

        // Convert Persian date to Gregorian
        if (displayDate && displayDate.value) {
            hiddenDate.value = convertPersianToGregorian(displayDate.value);
        }

        // Make sure the converted date is included in the FormData
        formData.set('transaction_date', hiddenDate.value);

        // Validate date before sending
        if (!hiddenDate.value) {
            let messageEarly = document.getElementById('txMessage');

            messageEarly.classList.remove('d-none');
            messageEarly.className = 'alert alert-danger py-2 px-3 small';
            messageEarly.textContent = 'لطفاً تاریخ تراکنش را انتخاب کنید';

            return;
        }

        let id = document.getElementById('tx_id').value;

        let url = id
            ? "<?= site_url('api/transaction/update/') ?>" + id
            : "<?= site_url('api/transaction/create') ?>";

        let message = document.getElementById('txMessage');
        let button = form.querySelector('button[type="submit"]');

        button.disabled = true;
        button.textContent = 'در حال ذخیره...';

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(function (res) {
                return res.text();
            })
            .then(function (text) {
                console.log('SERVER RESPONSE:', text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    console.error('INVALID JSON:', error);
                    throw new Error('Server did not return valid JSON');
                }

                message.classList.remove('d-none');
                message.textContent = data.message || '';
                message.className = data.status
                    ? 'alert alert-success py-2 px-3 small'
                    : 'alert alert-danger py-2 px-3 small';

                if (!data.status) {
                    return;
                }

                let modalElement = document.getElementById('transactionModal');
                let modal = bootstrap.Modal.getInstance(modalElement);

                if (modal) {
                    modal.hide();
                }

                modalElement.addEventListener(
                    'hidden.bs.modal',
                    function handleModalHidden() {

                        modalElement.removeEventListener(
                            'hidden.bs.modal',
                            handleModalHidden
                        );

                        document.querySelectorAll('.modal-backdrop').forEach(
                            function (backdrop) {
                                backdrop.remove();
                            }
                        );

                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');

                        let isCreate = !id;

                        if (typeof loadTransactions === 'function') {
                            loadTransactions(
                                isCreate
                                    ? 1
                                    : (typeof currentPage !== 'undefined'
                                        ? currentPage
                                        : 1)
                            );
                        }

                        if (typeof loadDashboardSummary === 'function') {
                            loadDashboardSummary();
                        }
                    }
                );

                form.reset();
                document.getElementById('tx_id').value = '';
            })
            .catch(function (error) {
                console.error('TRANSACTION ERROR:', error);

                message.classList.remove('d-none');
                message.className = 'alert alert-danger py-2 px-3 small';
                message.textContent = 'خطا در ارتباط با سرور';
            })
            .finally(function () {
                button.disabled = false;
                button.textContent = 'ذخیره تراکنش';
            });
    });




    let amountInput = document.getElementById('tx_amount');

    if (amountInput) {
        amountInput.addEventListener('input', function () {
            let cursorPosition = this.selectionStart;
            let oldValue = this.value;

            this.value = formatAmountInput(this.value);

            let lengthDifference = this.value.length - oldValue.length;

            this.setSelectionRange(
                cursorPosition + lengthDifference,
                cursorPosition + lengthDifference
            );
        });
    }


    categoryForm.addEventListener('submit', function (event) {
        event.preventDefault();

        let id = categoryId.value;
        let title = categoryTitle.value.trim();
        let type = categoryType.value;

        if (!title) {
            return;
        }

        let url = id
            ? "<?= site_url('api/categories/update/') ?>" + id
            : "<?= site_url('api/categories/create') ?>";

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({title: title, type: type})
        })
            .then(function (response) {
                return response.json();
            })
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
