<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

    <div class="container py-4">
        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success py-2 px-3 small border-0 mb-4" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="bi bi-check-circle ms-1"></i> <?= html_escape($this->session->flashdata('success')); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger py-2 px-3 small border-0 mb-4" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert">
                <i class="bi bi-exclamation-triangle ms-1"></i> <?= html_escape($this->session->flashdata('error')); ?>
            </div>
        <?php endif; ?>

        <!-- Header Actions -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold text-white mb-1">داشبورد مالی</h2>
                <p class="text-muted small mb-0">خوش آمدید، <?= html_escape($this->session->userdata('user_name') ?? 'کاربر') ?>. خلاصه وضعیت دخل و خرج این ماه:</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-orange-outline btn-sm px-3" data-bs-toggle="modal" data-bs-target="#editBudgetModal">
                    <i class="bi bi-piggy-bank me-1"></i> تنظیم بودجه
                </button>
                <button class="btn btn-orange-outline btn-sm px-3" data-bs-toggle="modal" data-bs-target="#categoryManagerModal">
                    <i class="bi bi-tags me-1"></i> دسته‌بندی‌ها
                </button>
                <button class="btn btn-orange-glow btn-sm px-3" data-bs-toggle="modal" data-bs-target="#transactionModal" onclick="openCreateTransactionModal()">
                    <i class="bi bi-plus-circle me-1"></i> ثبت تراکنش جدید
                </button>
            </div>
        </div>

        <!-- Component: Summary Cards (Budget, Monthly Income, Monthly Expense, Remaining) -->
        <?php $this->load->view('components/dashboard_summary'); ?>

        <!-- Component: Transactions Table & Filters -->
        <?php $this->load->view('components/dashboard_recent_table'); ?>
    </div>

    <!-- Component: Modals (Budget, Transaction Add/Edit, Category Manager, Delete Confirmation) -->
<?php $this->load->view('components/budget_modal'); ?>

<?php $this->load->view('components/footer'); ?>