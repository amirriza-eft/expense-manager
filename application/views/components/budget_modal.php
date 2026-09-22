<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Modal: Create / Edit Transaction -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel text-white" style="background: #1e1e24; border: 1px solid var(--border-subtle);">
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
                        <input type="text" class="form-control" name="title" id="tx_title" required placeholder="مثلاً: حقوق ماهانه، خرید سوپرمارکت">
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
                                <option value="">همه دسته‌بندی‌ها</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="tx_amount" class="form-label small text-muted">مبلغ (ریال)</label>
                            <input type="number" class="form-control" name="amount" id="tx_amount" required min="1" placeholder="مثال: 5000000">
                        </div>
                        <div class="col-6">
                            <label for="tx_date" class="form-label small text-muted">تاریخ تراکنش</label>
                            <input type="date" class="form-control" name="transaction_date" id="tx_date" required value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="tx_description" class="form-label small text-muted">توضیحات (اختیاری)</label>
                        <textarea class="form-control" name="description" id="tx_description" rows="2" placeholder="توضیحات تکمیلی پیرامون این تراکنش..."></textarea>
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
        <div class="modal-content glass-panel text-white" style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold mb-2">حذف تراکنش</h6>
                <p class="text-muted small">آیا از حذف این تراکنش از سوابق مالی اطمینان دارید؟</p>
                <form action="<?= site_url('home/delete_transaction') ?>" method="POST">
                    <input type="hidden" name="id" id="delete_tx_id">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-danger">بله، حذف کن</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Category Manager -->
<div class="modal fade" id="categoryManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel text-white" style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">مدیریت دسته‌بندی‌های شما</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add new category form inside modal -->
                <form action="<?= site_url('home/save_category') ?>" method="POST" class="row g-2 align-items-end mb-4 p-3 rounded" style="background: rgba(255,255,255,0.03);">
                    <input type="hidden" name="id" id="cat_id" value="">
                    <div class="col-12 col-md-8">
                        <label class="form-label small text-muted">نام دسته</label>
                        <input type="text" class="form-control" name="name" id="cat_name" placeholder="مثلاً: غذا، ورزش، حقوق" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-orange-glow w-100" id="catSubmitBtn">افزودن دسته</button>
                    </div>
                </form>

                <!-- Category List (all together) -->
                <ul class="list-group list-group-flush p-0">
                    <?php if(!empty($categories)): foreach($categories as $cat): ?>
                        <li class="list-group-item bg-transparent text-white border-bottom d-flex justify-content-between align-items-center py-2 px-1" style="border-color: var(--border-subtle) !important;">
                            <span class="small"><?= html_escape($cat->name) ?></span>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-sm btn-outline-secondary" onclick='setEditCategory(<?= json_encode($cat) ?>)'><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteCat(<?= $cat->id ?>)"><i class="bi bi-trash"></i></button>
                            </div>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Category Confirmation -->
<div class="modal fade" id="deleteCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-panel text-white" style="background: #1e1e24; border: 1px solid var(--border-subtle);">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash text-danger fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold mb-2">حذف دسته‌بندی</h6>
                <p class="text-muted small">آیا از حذف این دسته‌بندی اطمینان دارید؟</p>
                <form action="<?= site_url('home/delete_category') ?>" method="POST">
                    <input type="hidden" name="id" id="del_cat_id_input">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
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


        document.getElementById('txForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const message = document.getElementById('txMessage');
        const button = form.querySelector('button[type="submit"]');

        message.classList.add('d-none');

        button.disabled = true;
        button.textContent = 'در حال ذخیره...';

        fetch("<?= site_url('api/transactions') ?>", {
        method: 'POST',
        body: new FormData(form)
    })
        .then(response => response.json())
        .then(data => {
        message.textContent = data.message;
        message.className = data.status
        ? 'alert alert-success py-2 px-3 small'
        : 'alert alert-danger py-2 px-3 small';

        if (data.status) {
        form.reset();

        setTimeout(() => {
        location.reload();
    }, 700);
    }
    })
        .catch(() => {
        message.textContent = 'خطا در ارتباط با سرور';
        message.className = 'alert alert-danger py-2 px-3 small';
    })
        .finally(() => {
        button.disabled = false;
        button.textContent = 'ذخیره تراکنش';
    });
    });


    //== categories ==//
    fetch('/api/categories')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('tx_category');

            data.categories.forEach(category => {
                const option = document.createElement('option');

                option.value = category.id;
                option.textContent = category.name;
                option.dataset.type = category.type;

                categorySelect.appendChild(option);
            });
        });

</script>