<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 mb-4">

    <div class="row g-2 align-items-center">

        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>

                <input
                        type="text"
                        id="search"
                        class="form-control"
                        placeholder="جستجو..."
                >
            </div>
        </div>


        <div class="col-6 col-md-3">
            <select id="filterType" class="form-select">
                <option value="">همه نوع‌ها</option>
                <option value="income">درآمد</option>
                <option value="expense">هزینه</option>
            </select>
        </div>


        <div class="col-6 col-md-3">
            <select id="filterCategory" class="form-select">
                <option value="">همه دسته‌ها</option>
            </select>
        </div>


        <div class="col-12 col-md-2">
            <button
                    class="btn btn-orange-outline w-100"
                    onclick="loadTransactions()"
            >
                اعمال
            </button>
        </div>

    </div>

</div>



<div class="d-flex flex-wrap gap-2 mb-4">

    <button
            class="btn btn-orange-outline"
            data-bs-toggle="modal"
            data-bs-target="#categoryManagerModal"
    >
        <i class="bi bi-tags"></i>
        مدیریت دسته‌ها
    </button>


    <button
            class="btn btn-orange-glow"
            data-bs-toggle="modal"
            data-bs-target="#transactionModal"
            onclick="openCreateTransactionModal()"
    >
        <i class="bi bi-plus-circle"></i>
        ثبت تراکنش
    </button>

</div>



<div class="glass-panel p-4">

    <div class="d-flex justify-content-between mb-3">

        <h5 class="fw-bold text-white mb-0">
            لیست تراکنش‌ها
        </h5>

        <span id="transactionCount" class="text-muted small">
        </span>

    </div>


    <div class="table-responsive">

        <table class="table table-dark align-middle">

            <thead>

            <tr class="text-muted small">

                <th>عنوان</th>
                <th>نوع</th>
                <th>دسته</th>
                <th>مبلغ</th>
                <th>تاریخ</th>
                <th>توضیحات</th>
                <th></th>

            </tr>

            </thead>


            <tbody id="transactionList">

            </tbody>


        </table>

    </div>

</div>



<script>

    document.addEventListener('DOMContentLoaded',()=>{

        loadCategories();
        loadTransactions();

    });


    function loadCategories()
    {
        fetch("<?= site_url('api/categories') ?>")
            .then(res=>res.json())
            .then(data=>{

                let select=document.getElementById('filterCategory');

                select.innerHTML=
                    '<option value="">همه دسته‌ها</option>';


                if(data.status)
                {
                    data.categories.forEach(cat=>{

                        select.innerHTML+=`
                    <option value="${cat.id}">
                        ${cat.title}
                    </option>
                `;

                    });
                }

            });
    }



    function loadTransactions()
    {
        let params=new URLSearchParams({

            search:
            document.getElementById('search').value,

            type:
            document.getElementById('filterType').value,

            category_id:
            document.getElementById('filterCategory').value

        });


        fetch("<?= site_url('api/transaction') ?>?"+params)
            .then(res=>res.json())
            .then(data=>{

                let tbody=document.getElementById('transactionList');

                tbody.innerHTML='';


                if(!data.status || !data.transactions.length)
                {
                    tbody.innerHTML=`
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    تراکنشی وجود ندارد
                </td>
            </tr>`;

                    return;
                }


                document.getElementById('transactionCount').innerText =
                    "تعداد: "+data.transactions.length;


                data.transactions.forEach(tx=>{

                    tbody.innerHTML+=`

            <tr>

                <td class="text-white fw-bold">
                    ${tx.title}
                </td>


                <td>
                    ${
                        tx.type==="income"
                            ?
                            '<span class="badge badge-income">درآمد</span>'
                            :
                            '<span class="badge badge-expense">هزینه</span>'
                    }
                </td>


                <td>
                    <span class="badge bg-secondary">
                        ${tx.category_name ?? 'بدون دسته'}
                    </span>
                </td>


                <td class="${
                        tx.type==="income"
                            ?
                            'text-success'
                            :
                            'text-danger'
                    } fw-bold">

                    ${
                        tx.type==="income"
                            ? '+'
                            : '-'
                    }

                    ${Number(tx.amount).toLocaleString()}

                </td>


                <td class="text-muted small">
                    ${tx.transaction_date}
                </td>


                <td class="text-muted small">
                    ${tx.description ?? '-'}
                </td>


                <td>

                    <button
                    class="btn btn-sm btn-outline-secondary"
                    onclick='openEditTransactionModal(${JSON.stringify(tx)})'
                    >
                    <i class="bi bi-pencil"></i>
                    </button>


                    <button
                    class="btn btn-sm btn-outline-danger"
                    onclick="confirmDeleteTransaction(${tx.id})"
                    >
                    <i class="bi bi-trash"></i>
                    </button>

                </td>


            </tr>

            `;

                });

            });
    }

</script>