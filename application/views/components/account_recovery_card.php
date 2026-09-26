<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="recoveryPanel" class="glass-panel p-4 p-sm-5 shadow-lg border d-none"
     style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-5">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">بازیابی حساب</h3>
        <p class="text-muted small">ایمیل و رمز عبور حساب حذف‌شده خود را وارد کنید</p>
    </div>

    <div id="recoveryError" class="alert alert-danger py-2 px-3 small border-0 d-none"></div>
    <div id="recoverySuccess" class="alert alert-success py-2 px-3 small border-0 d-none"></div>

    <form id="recoveryForm">
        <div class="mb-3">
            <label class="form-label text-light small fw-medium" for="recovery_email">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="recovery_email"
                       placeholder="name@example.com" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium" for="recovery_password">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" class="form-control" name="password" id="recovery_password"
                       placeholder="••••••••" required>
                <button class="input-group-text toggle-password" type="button" data-target="recovery_password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" id="recoverySubmitBtn">
            بازیابی حساب
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <button type="button" class="btn btn-link text-decoration-none small fw-bold text-accent-orange p-0"
                id="backToLoginBtn">
            بازگشت به ورود
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let recoveryForm = document.getElementById('recoveryForm');
        let recoveryError = document.getElementById('recoveryError');
        let recoverySuccess = document.getElementById('recoverySuccess');
        let recoverySubmitBtn = document.getElementById('recoverySubmitBtn');
        let backToLoginBtn = document.getElementById('backToLoginBtn');

        backToLoginBtn.addEventListener('click', function () {
            if (typeof showLoginPanel === 'function') {
                showLoginPanel(document.getElementById('recovery_email').value);
            }
        });

        recoveryForm.addEventListener('submit', function (e) {
            e.preventDefault();

            recoveryError.classList.add('d-none');
            recoverySuccess.classList.add('d-none');
            recoverySubmitBtn.disabled = true;
            recoverySubmitBtn.textContent = 'در حال بازیابی...';

            fetch("<?= site_url('api/auth/restore') ?>", {
                method: 'POST',
                body: new FormData(this)
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.status) {
                        recoverySuccess.textContent = data.message;
                        recoverySuccess.classList.remove('d-none');

                        setTimeout(function () {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 1200);
                        return;
                    }

                    recoveryError.innerHTML = data.message;
                    recoveryError.classList.remove('d-none');
                })
                .catch(function () {
                    recoveryError.textContent = 'خطا در ارتباط با سرور';
                    recoveryError.classList.remove('d-none');
                })
                .finally(function () {
                    recoverySubmitBtn.disabled = false;
                    recoverySubmitBtn.textContent = 'بازیابی حساب';
                });
        });
    });
</script>
