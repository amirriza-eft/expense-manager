<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="fw-bold text-white mb-4">پروفایل کاربری</h3>

                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success py-2 px-3 small border-0 mb-4" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                        <i class="bi bi-check-circle ms-1"></i> <?= html_escape($this->session->flashdata('success')); ?>
                    </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger py-2 px-3 small border-0 mb-4" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;">
                        <i class="bi bi-exclamation-triangle ms-1"></i> <?= html_escape($this->session->flashdata('error')); ?>
                    </div>
                <?php endif; ?>

                <!-- Component: Information & Avatar Upload -->
                <?php $this->load->view('components/profile_info_card'); ?>

                <!-- Component: Change Password Form -->
                <?php $this->load->view('components/profile_password_card'); ?>
            </div>
        </div>
    </div>

<?php $this->load->view('components/footer'); ?>