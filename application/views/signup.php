<section class="py-5 min-vh-100 d-flex align-items-center position-relative">
    <!-- Glow Backdrop -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 450px; height: 450px; background: radial-gradient(circle, rgba(255,107,0,0.15) 0%, rgba(18,18,18,0) 70%); pointer-events: none;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <!-- Auth Card -->
                <div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
                    <div class="text-center mb-4">
                        <a href="index.html" class="d-inline-block mb-3">
                            <i class="bi bi-fire brand-icon" style="font-size: 2.75rem;"></i>
                        </a>
                        <h3 class="fw-bold text-white mb-2">ایجاد حساب جدید</h3>
                        <p class="text-muted small">اطلاعات خواسته شده را جهت پیوستن به پلتفرم تکمیل فرمایید</p>
                    </div>

                    <!-- Client side error banner -->
                    <div id="clientErrorAlert" class="alert alert-danger py-2 px-3 small border-0 d-none" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert"></div>

                    <form action="/register" method="POST" id="registerForm">
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="fullname" class="form-label text-light small fw-medium">نام و نام خانوادگی</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="fullname" id="fullname" placeholder="مثلاً: علی محمدی" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="reg_email" class="form-label text-light small fw-medium">آدرس ایمیل</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="reg_email" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="reg_password" class="form-label text-light small fw-medium">کلمه عبور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="reg_password" placeholder="حداقل ۸ کاراکتر" minlength="8" required>
                                <button class="input-group-text toggle-password" type="button" data-target="reg_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label text-light small fw-medium">تکرار کلمه عبور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="تکرار رمز عبور" required>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="terms" id="termsCheck" required>
                            <label class="form-check-label text-muted small user-select-none" for="termsCheck">
                                کلیه <a href="#" class="text-decoration-none" style="color: var(--accent-orange);">شرایط و قوانین و مقررات</a> را مطالعه نموده و می‌پذیرم.
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-orange-glow w-100 py-2 fw-bold" id="regSubmitBtn">
                            <span class="btn-text">ثبت نام و ایجاد حساب</span>
                            <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
                        <span class="text-muted small">قبلاً ثبت‌نام کرده‌اید؟ </span>
                        <a href="login.html" class="text-decoration-none small fw-bold" style="color: var(--accent-orange);">وارد شوید</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vanilla JS Client-side Validation -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Show/Hide Password
        const toggleBtns = document.querySelectorAll('.toggle-password');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });

        // Password Confirmation & Validation Checks
        const form = document.getElementById('registerForm');
        const pwd = document.getElementById('reg_password');
        const pwdConfirm = document.getElementById('password_confirm');
        const errorAlert = document.getElementById('clientErrorAlert');
        const submitBtn = document.getElementById('regSubmitBtn');

        form.addEventListener('submit', function(e) {
            errorAlert.classList.add('d-none');
            errorAlert.textContent = '';

            if (pwd.value !== pwdConfirm.value) {
                e.preventDefault();
                errorAlert.textContent = 'کلمه عبور با تکرار آن همخوانی ندارد.';
                errorAlert.classList.remove('d-none');
                pwdConfirm.focus();
                return false;
            }

            if (pwd.value.length < 8) {
                e.preventDefault();
                errorAlert.textContent = 'کلمه عبور باید حداقل شامل ۸ کاراکتر باشد.';
                errorAlert.classList.remove('d-none');
                pwd.focus();
                return false;
            }

            // Show loading indicator
            submitBtn.querySelector('.spinner-border').classList.remove('d-none');
            submitBtn.setAttribute('disabled', 'disabled');
        });
    });
</script>