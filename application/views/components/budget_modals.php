<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/transaction_modal');
$this->load->view('components/delete_transaction_modal');
$this->load->view('components/category_manager_modal');
?>

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
