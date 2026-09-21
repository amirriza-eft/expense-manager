<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-4">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size: 2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">ثبت‌نام در حساب‌یار</h3>
        <p class="text-muted small">مدیریت هوشمند درآمدها و هزینه‌های خود را آغاز کنید</p>
    </div>

    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger py-2 px-3 small border-0" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert">
            <i class="bi bi-exclamation-triangle-fill ms-1"></i> <?= html_escape($this->session->flashdata('error')); ?>
        </div>
    <?php endif; ?>

    <div id="clientErrorAlert" class="alert alert-danger py-2 px-3 small border-0 d-none" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert"></div>

    <form action="<?= site_url('auth/register') ?>" method="POST" id="registerForm">
        <div class="mb-3">
            <label for="fullname" class="form-label text-light small fw-medium">نام و نام خانوادگی</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="full_name" id="fullname" placeholder="مثال: علی محمدی" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="reg_email" class="form-label text-light small fw-medium">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="reg_email" placeholder="name@example.com" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="reg_password" class="form-label text-light small fw-medium">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" class="form-control" name="password" id="reg_password" placeholder="حداقل ۶ کاراکتر" minlength="6" required>
                <button class="input-group-text toggle-password" type="button" data-target="reg_password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirm" class="form-label text-light small fw-medium">تکرار رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="تکرار مجدد رمز عبور" required>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100 py-2 fw-bold" id="regSubmitBtn">
            <span>ایجاد حساب کاربری</span>
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <span class="text-muted small">قبلاً حساب ساخته‌اید؟ </span>
        <a href="<?= site_url('login') ?>" class="text-decoration-none small fw-bold" style="color: var(--accent-orange);">وارد شوید</a>
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

        const form = document.getElementById('registerForm');
        const pwd = document.getElementById('reg_password');
        const pwdConfirm = document.getElementById('password_confirm');
        const errorAlert = document.getElementById('clientErrorAlert');

        form.addEventListener('submit', function(e) {
            errorAlert.classList.add('d-none');
            if (pwd.value !== pwdConfirm.value) {
                e.preventDefault();
                errorAlert.textContent = 'رمز عبور و تکرار آن یکسان نیستند.';
                errorAlert.classList.remove('d-none');
                pwdConfirm.focus();
                return false;
            }
        });
    });
</script>