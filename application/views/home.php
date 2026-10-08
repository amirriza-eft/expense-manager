<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

<div class="container py-4">

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

    <?php $this->load->view('dashboard/summary'); ?>
    <br>
    <?php $this->load->view('transactions/ta_index'); ?>
</div>

<?php $this->load->view('dashboard/line_chart'); ?>
<?php $this->load->view('components/footer'); ?>
