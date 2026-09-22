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
                            <select id="filterCategory" class="form-select">
                                <option value="">همه دسته‌ها</option>
                            </select>
                        </div>

                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_amount" class="form-label small text-muted">مبلغ (تومان)</label>
                            <input type="number" class="form-control" name="amount" id="tx_amount" required min="1"
                                   placeholder="مثال: 5000000">
                        </div>
                        <div class="col-6">
                            <label for="tx_date" class="form-label small text-muted">تاریخ تراکنش</label>
                            <input type="date" class="form-control" name="transaction_date" id="tx_date" required
                                   value="<?= date('Y-m-d') ?>">
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
                <form id="deleteTransactionForm">
                    <input type="hidden" name="id" id="delete_tx_id">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            انصراف
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                onclick="deleteTransaction()">
                            بله، حذف کن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal: Category Manager -->
<div class="modal fade" id="categoryManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div
                class="modal-content glass-panel text-white"
                style="background: #1e1e24; border: 1px solid var(--border-subtle);"
        >

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    مدیریت دسته‌بندی‌های شما
                </h5>

                <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                ></button>
            </div>

            <div class="modal-body">

                <!-- Add / Edit category -->
                <form
                        id="categoryForm"
                        class="row g-2 align-items-end mb-4 p-3 rounded"
                        style="background: rgba(255,255,255,0.03);"
                >

                    <input
                            type="hidden"
                            id="cat_id"
                            value=""
                    >

                    <div class="col-12 col-md-8">
                        <label class="form-label small text-muted">
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

                    <div class="col-12 col-md-4">
                        <button
                                type="submit"
                                class="btn btn-orange-glow w-100"
                                id="catSubmitBtn"
                        >
                            افزودن دسته
                        </button>
                    </div>

                </form>

                <!-- Category list -->
                <ul
                        id="categoryList"
                        class="list-group list-group-flush p-0"
                >
                    <!-- JavaScript will add categories here -->
                </ul>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Category Confirmation -->
<div class="modal fade" id="deleteCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-panel text-white"
             style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash text-danger fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold mb-2">حذف دسته‌بندی</h6>
                <p class="text-muted small">آیا از حذف این دسته‌بندی اطمینان دارید؟</p>
                <form action="<?= site_url('home/delete_category') ?>" method="POST">
                    <input type="hidden" name="id" id="del_cat_id_input">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                onclick="deleteCategoryConfirm()">
                            حذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Transaction Modal Handling
    function openCreateTransactionModal() {
        document.getElementById('txModalTitle').innerText = 'ثبت تراکنش جدید';
        document.getElementById('txForm').reset();
        document.getElementById('tx_id').value = '';
        document.getElementById('tx_date').value = new Date().toISOString().split('T')[0];
        filterCategoryDropdownByType();
    }

    function openEditTransactionModal(tx) {
        document.getElementById('txModalTitle').innerText = 'ویرایش تراکنش';
        document.getElementById('tx_id').value = tx.id;
        document.getElementById('tx_title').value = tx.title;
        document.getElementById('tx_type').value = tx.type;
        filterCategoryDropdownByType();
        document.getElementById('tx_category').value = tx.category_id;
        document.getElementById('tx_amount').value = tx.amount;
        document.getElementById('tx_date').value = tx.transaction_date;
        document.getElementById('tx_description').value = tx.description || '';

        new bootstrap.Modal(document.getElementById('transactionModal')).show();
    }

    function filterCategoryDropdownByType() {
        const selectedType = document.getElementById('tx_type').value;
        const catSelect = document.getElementById('tx_category');
        const options = catSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) return; // Keep "انتخاب دسته..."
            if (opt.getAttribute('data-type') === selectedType) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function confirmDeleteTransaction(id) {
        document.getElementById('delete_tx_id').value = id;
        new bootstrap.Modal(document.getElementById('deleteTxConfirmModal')).show();
    }

    // Category Modal Handling
    function setEditCategory(cat) {
        document.getElementById('cat_id').value = cat.id;
        document.getElementById('cat_name').value = cat.name;
        document.getElementById('cat_type').value = cat.type;
        document.getElementById('catSubmitBtn').innerText = 'به‌روزرسانی دسته';
    }

    function confirmDeleteCat(id) {
        document.getElementById('del_cat_id_input').value = id;
        new bootstrap.Modal(document.getElementById('deleteCatModal')).show();
    }


    document.getElementById('txForm').addEventListener('submit', function (e) {

        e.preventDefault();

        const form = this;
        const id = document.getElementById('tx_id').value;

        let url = id
            ? "<?= site_url('api/transaction/update/') ?>" + id
            : "<?= site_url('api/transaction/create') ?>";


        const message = document.getElementById('txMessage');
        const button = form.querySelector('button[type="submit"]');


        button.disabled = true;
        button.textContent = 'در حال ذخیره...';


        fetch(url, {
            method: 'POST',
            body: new FormData(form)
        })

            .then(res => res.json())

            .then(data => {

                message.classList.remove('d-none');

                message.textContent = data.message;

                message.className = data.status
                    ? 'alert alert-success py-2 px-3 small'
                    : 'alert alert-danger py-2 px-3 small';

                if (data.status) {

                    setTimeout(() => {

                        location.reload();

                    }, 700);

                }

            })

            .catch(() => {

                message.className = 'alert alert-danger py-2 px-3 small';
                message.textContent = 'خطا در ارتباط با سرور';

            })

            .finally(() => {

                button.disabled = false;
                button.textContent = 'ذخیره تراکنش';

            });

    });


    //== categories ==//
    // ==================== Categories ====================

    const categoryForm = document.getElementById('categoryForm');
    const categoryList = document.getElementById('categoryList');
    const categoryId = document.getElementById('cat_id');
    const categoryTitle = document.getElementById('cat_title');
    const categorySubmitBtn = document.getElementById('catSubmitBtn');


    // Create / update category
    categoryForm.addEventListener('submit', function (event) {

        event.preventDefault();

        const id = categoryId.value;
        const title = categoryTitle.value.trim();

        if (!title) {
            return;
        }

        const url = id
            ? `/api/categories/update/${id}`
            : '/api/categories/create';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                title: title
            })
        })
            .then(response => response.json())
            .then(data => {

                console.log(data);

                if (!data.status) {
                    alert(data.message);
                    return;
                }

                resetCategoryForm();
                loadCategories();
            })
            .catch(error => {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    });


    // Load categories
    function loadCategories() {
        fetch("<?= site_url('api/categories') ?>")
            .then(res => res.json())
            .then(data => {

                let filter = document.getElementById('filterCategory');
                let transaction = document.getElementById('tx_category');


                filter.innerHTML =
                    '<option value="">همه دسته‌ها</option>';


                transaction.innerHTML =
                    '<option value="">بدون دسته</option>';


                if (data.status) {
                    data.categories.forEach(cat => {

                        filter.innerHTML += `
                <option value="${cat.id}">
                    ${cat.title}
                </option>`;


                        transaction.innerHTML += `
                <option value="${cat.id}">
                    ${cat.title}
                </option>`;

                    });
                }

            });
    }


    // Render category list
    function renderCategories(categories) {

        categoryList.innerHTML = '';

        categories.forEach(category => {

            const li = document.createElement('li');

            li.className =
                'list-group-item bg-transparent text-white border-bottom d-flex justify-content-between align-items-center py-2 px-1';

            li.innerHTML = `
            <span class="small"></span>

            <div class="btn-group btn-group-sm">

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary edit-category-btn">
                    <i class="bi bi-pencil"></i>
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger delete-category-btn">
                    <i class="bi bi-trash"></i>
                </button>

            </div>
        `;

            li.querySelector('span').textContent = category.title;

            li.querySelector('.edit-category-btn')
                .addEventListener('click', () => {
                    editCategory(category);
                });

            li.querySelector('.delete-category-btn')
                .addEventListener('click', () => {
                    deleteCategory(category.id);
                });

            categoryList.appendChild(li);
        });
    }


    ///=/////////////////==///
    // delete btn //
    let deleteTransactionId = null;

    function confirmDeleteTransaction(id) {
        deleteTransactionId = id;

        new bootstrap.Modal(
            document.getElementById('deleteTxConfirmModal')
        ).show();
    }

    function deleteTransaction() {
        fetch("<?= site_url('api/transaction/delete') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                id: deleteTransactionId
            })
        })
            .then(res => res.json())
            .then(data => {

                if (data.status) {
                    bootstrap.Modal
                        .getInstance(
                            document.getElementById('deleteTxConfirmModal')
                        )
                        .hide();

                    loadTransactions();
                } else {
                    alert(data.message);
                }

            })
            .catch(err => {
                console.error(err);
                alert('خطا در حذف تراکنش');
            });
    }

    // Edit category
    function editCategory(category) {

        categoryId.value = category.id;
        categoryTitle.value = category.title;

        categorySubmitBtn.textContent = 'به‌روزرسانی دسته';
        categoryTitle.focus();
    }


    // Delete category
    function deleteCategory(id) {

        if (!confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')) {
            return;
        }

        fetch(`/api/categories/delete/${id}`, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {

                if (!data.status) {
                    alert(data.message);
                    return;
                }

                loadCategories();
            })
            .catch(error => {
                console.error(error);
                alert('خطا در ارتباط با سرور');
            });
    }


    // Reset form
    function resetCategoryForm() {

        categoryId.value = '';
        categoryTitle.value = '';

        categorySubmitBtn.textContent = 'افزودن دسته';
    }


    // Put categories into transaction dropdown
    function loadCategoriesIntoTransactionDropdown(categories) {

        const categorySelect = document.getElementById('tx_category');

        categorySelect.innerHTML = `
        <option value="">همه دسته‌بندی‌ها</option>
    `;

        categories.forEach(category => {

            const option = document.createElement('option');

            option.value = category.id;
            option.textContent = category.title;

            categorySelect.appendChild(option);
        });
    }


    // Initial load
    loadCategories();

</script>