<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="loginPanel" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-5">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">ورود به حساب‌یار</h3>
        <p class="text-muted small">برای دسترسی به پنل مدیریت مالی وارد حساب خود شوید</p>
    </div>

    <div id="loginError" class="alert alert-danger py-2 px-3 small border-0 d-none"></div>
    <div id="loginSuccess" class="alert alert-success py-2 px-3 small border-0 d-none"></div>
    <div id="deletedAccountNotice" class="alert alert-danger py-2 px-3 small border-0 d-none">
        <div class="mb-2" id="deletedAccountMessage"></div>
        <button type="button" class="btn btn-orange-outline btn-sm" id="showRecoveryBtn">
            <i class="bi bi-arrow-counterclockwise"></i>
            بازیابی حساب
        </button>
    </div>

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

        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div class="form-check remember-wrapper">
                <input
                        type="checkbox"
                        value="1"
                        id="rememberMe"
                        name="remember_me"
                >
                <label for="rememberMe">
                    مرا به خاطر بسپار
                </label>
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

<?php $this->load->view('components/account_recovery_card'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginPanel = document.getElementById('loginPanel');
        var recoveryPanel = document.getElementById('recoveryPanel');
        var passwordInput = document.getElementById('password');
        var loginForm = document.getElementById('loginForm');
        var errorBox = document.getElementById('loginError');
        var successBox = document.getElementById('loginSuccess');
        var deletedNotice = document.getElementById('deletedAccountNotice');
        var deletedMessage = document.getElementById('deletedAccountMessage');
        var submitBtn = document.getElementById('submitBtn');
        var showRecoveryBtn = document.getElementById('showRecoveryBtn');
        var recoveryEmail = document.getElementById('recovery_email');
        var recoveryPassword = document.getElementById('recovery_password');

        function bindPasswordToggle(button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.target);
                var icon = this.querySelector('i');
                if (!input) {
                    return;
                }
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        }

        document.querySelectorAll('.toggle-password').forEach(bindPasswordToggle);

        function showLoginPanel(prefillEmail) {
            recoveryPanel.classList.add('d-none');
            loginPanel.classList.remove('d-none');
            if (prefillEmail) {
                document.getElementById('email').value = prefillEmail;
            }
        }

        function showRecoveryPanel(prefillEmail, prefillPassword) {
            deletedNotice.classList.add('d-none');
            errorBox.classList.add('d-none');
            successBox.classList.add('d-none');
            loginPanel.classList.add('d-none');
            recoveryPanel.classList.remove('d-none');
            recoveryEmail.value = prefillEmail || '';
            recoveryPassword.value = prefillPassword || '';
        }

        window.showLoginPanel = showLoginPanel;
        window.showRecoveryPanel = showRecoveryPanel;

        showRecoveryBtn.addEventListener('click', function () {
            showRecoveryPanel(
                document.getElementById('email').value,
                passwordInput.value
            );
        });

        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            errorBox.classList.add('d-none');
            successBox.classList.add('d-none');
            deletedNotice.classList.add('d-none');
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
                        return;
                    }

                    if (data.code === 'account_deleted') {
                        deletedMessage.textContent = data.message;
                        deletedNotice.classList.remove('d-none');
                        return;
                    }

                    errorBox.innerHTML = data.message;
                    errorBox.classList.remove('d-none');
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
