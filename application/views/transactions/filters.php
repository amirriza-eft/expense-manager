<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="search" class="form-control" placeholder="جستجو...">
            </div>
        </div>

        <div class="col-6 col-md-2">
            <select id="filterType" class="form-select">
                <option value="">همه نوع‌ها</option>
                <option value="income">درآمد</option>
                <option value="expense">هزینه</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <select id="filterCategory" class="form-select">
                <option value="">همه دسته‌ها</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <input type="text" id="filterFromDate" class="form-control" placeholder="از تاریخ" autocomplete="off">
        </div>

        <div class="col-6 col-md-2">
            <input type="text" id="filterToDate" class="form-control" placeholder="تا تاریخ" autocomplete="off">
        </div>

        <div class="col-12 col-md-1">
            <button type="button" class="btn btn-orange-outline w-100" id="applyFiltersBtn">
                اعمال
            </button>
        </div>
    </div>
</div>
