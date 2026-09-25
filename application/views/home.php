<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

<div class="container py-4">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success py-2 px-3 small border-0 mb-4">
            <i class="bi bi-check-circle ms-1"></i>
            <?= html_escape($this->session->flashdata('success')); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger py-2 px-3 small border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle ms-1"></i>
            <?= html_escape($this->session->flashdata('error')); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1">داشبورد مالی</h2>
            <p class="text-muted small mb-0">
                خوش آمدید،
                <?= html_escape($this->session->userdata('user_name') ?: ($this->session->userdata('full_name') ?: 'کاربر')) ?>.
                خلاصه وضعیت دخل و خرج این ماه:
            </p>
        </div>
    </div>

    <?php $this->load->view('components/dashboard_summary'); ?>
    <?php $this->load->view('components/dashboard_recent_table'); ?>
</div>

<?php $this->load->view('components/budget_modal'); ?>
<?php $this->load->view('components/footer'); ?>
