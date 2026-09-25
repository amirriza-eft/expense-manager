<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-5">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">ورود به حساب‌یار</h3>
        <p class="text-muted small">برای دسترسی به پنل مدیریت مالی وارد حساب خود شوید</p>
    </div>

    <div id="loginError" class="alert alert-danger py-2 px-3 small border-0 d-none"></div>
    <div id="loginSuccess" class="alert alert-success py-2 px-3 small border-0 d-none"></div>

    <form id="loginForm">
        <div class="mb-3">
            <label class="form-label text-light small fw-medium" for="email">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="email"
                       placeholder="name@example.com" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium" for="password">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" class="form-control" name="password" id="password"
                       placeholder="••••••••" required>
                <button class="input-group-text toggle-password" type="button" data-target="password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" id="submitBtn">ورود به حساب</button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <span class="text-muted small">حساب کاربری ندارید؟</span>
        <a href="<?= site_url('signup') ?>" class="text-decoration-none small fw-bold text-accent-orange">
            ثبت‌نام کنید
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var togglePassword = document.querySelector('.toggle-password');
        var loginForm = document.getElementById('loginForm');
        var errorBox = document.getElementById('loginError');
        var successBox = document.getElementById('loginSuccess');
        var submitBtn = document.getElementById('submitBtn');

        togglePassword.addEventListener('click', function () {
            var icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            errorBox.classList.add('d-none');
            successBox.classList.add('d-none');
            submitBtn.disabled = true;
            submitBtn.textContent = 'در حال ورود...';

            fetch("<?= site_url('api/auth/login') ?>", {
                method: 'POST',
                body: new FormData(this)
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.status) {
                        successBox.textContent = data.message;
                        successBox.classList.remove('d-none');
                        setTimeout(function () {
                            window.location.href = "<?= site_url('home') ?>";
                        }, 800);
                    } else {
                        errorBox.innerHTML = data.message;
                        errorBox.classList.remove('d-none');
                    }
                })
                .catch(function () {
                    errorBox.textContent = 'خطا در ارتباط با سرور';
                    errorBox.classList.remove('d-none');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'ورود به حساب';
                });
        });
    });
</script>
