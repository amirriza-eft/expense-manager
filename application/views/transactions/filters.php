<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-3 mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="search" class="form-control" placeholder="جستجو..."
                       v-model="filters.search" @keydown.enter.prevent="loadTransactions(1)">
            </div>
        </div>

        <div class="col-6 col-md-2">
            <select id="filterType" class="form-select" v-model="filters.type" @change="onFilterTypeChange">
                <option value="">همه نوع‌ها</option>
                <option value="income">درآمد</option>
                <option value="expense">هزینه</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <select id="filterCategory" class="form-select" v-model="filters.category_id">
                <option value="">همه دسته‌ها</option>
                <option v-for="category in filterCategories" :key="category.id" :value="category.id">{{ category.title }}</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <input type="text" id="filterFromDate" class="form-control" placeholder="از تاریخ" autocomplete="off"
                   ref="filterFromDate" v-model="filters.from_date">
        </div>

        <div class="col-6 col-md-2">
            <input type="text" id="filterToDate" class="form-control" placeholder="تا تاریخ" autocomplete="off"
                   ref="filterToDate" v-model="filters.to_date">
        </div>

        <div class="col-12 col-md-1">
            <button type="button" class="btn btn-orange-outline w-100" id="applyFiltersBtn" @click="loadTransactions(1)">
                اعمال
            </button>
        </div>
    </div>
</div>
