<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-4">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size: 2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">ورود به حساب‌یار</h3>
        <p class="text-muted small">برای دسترسی به پنل مدیریت مالی وارد حساب خود شوید</p>
    </div>

    <!-- CI Flash Alerts -->
    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger py-2 px-3 small border-0" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert">
            <i class="bi bi-exclamation-triangle-fill ms-1"></i> <?= html_escape($this->session->flashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success py-2 px-3 small border-0" style="background: rgba(16, 185, 129, 0.15); color: #10b981;" role="alert">
            <i class="bi bi-check-circle ms-1"></i> <?= html_escape($this->session->flashdata('success')); ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('auth/login_action') ?>" method="POST" id="loginForm" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label text-light small fw-medium">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required value="<?= html_escape($this->session->flashdata('old_email') ?? '') ?>">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label text-light small fw-medium">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                <button class="input-group-text toggle-password" type="button" data-target="password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100 py-2 fw-bold" id="submitBtn">
            <span>ورود به حساب</span>
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <span class="text-muted small">حساب کاربری ندارید؟ </span>
        <a href="<?= site_url('signup') ?>" class="text-decoration-none small fw-bold" style="color: var(--accent-orange);">ثبت‌نام کنید</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    });
</script>
