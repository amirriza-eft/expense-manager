<section class="py-5 min-vh-100 d-flex align-items-center position-relative">
    <!-- Glow Backdrop -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,107,0,0.15) 0%, rgba(18,18,18,0) 70%); pointer-events: none;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 col-xl-4">
                <!-- Auth Card -->
                <div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
                    <div class="text-center mb-4">
                        <a href="index.html" class="d-inline-block mb-3">
                            <i class="bi bi-fire brand-icon" style="font-size: 2.75rem;"></i>
                        </a>
                        <h3 class="fw-bold text-white mb-2">ورود به پنل</h3>
                        <p class="text-muted small">برای دسترسی به حساب کاربری، اطلاعات خود را وارد کنید</p>
                    </div>

                    <!-- Alert message container (hidden by default) -->
                    <div id="loginAlert" class="alert alert-danger py-2 px-3 small border-0 d-none" style="background: rgba(220, 53, 69, 0.15); color: #ff6b6b;" role="alert">
                        <i class="bi bi-exclamation-triangle-fill ms-1"></i>
                        <span id="loginAlertText"></span>
                    </div>

                    <form action="/login" method="POST" id="loginForm" novalidate>
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-light small fw-medium">آدرس ایمیل</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label text-light small fw-medium mb-0">کلمه عبور</label>
                                <a href="forgot-password.html" class="text-decoration-none small hover-orange" style="color: var(--accent-orange); font-size: 0.8rem;">
                                    فراموشی رمز عبور؟
                                </a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                                <button class="input-group-text toggle-password" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label text-muted small user-select-none" for="rememberMe">
                                مرا به خاطر بسپار
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-orange-glow w-100 py-2 fw-bold" id="submitBtn">
                            <span class="btn-text">ورود به حساب کاربری</span>
                            <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
                        <span class="text-muted small">حساب کاربری ندارید؟ </span>
                        <a href="register.html" class="text-decoration-none small fw-bold" style="color: var(--accent-orange);">ثبت نام کنید</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vanilla JS Password Toggle & Form Interaction -->
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

        // Form submission validation & UI loading state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const loginAlert = document.getElementById('loginAlert');
        const loginAlertText = document.getElementById('loginAlertText');

        form.addEventListener('submit', function(e) {
            loginAlert.classList.add('d-none');

            if (!form.checkValidity()) {
                e.preventDefault();
                loginAlertText.textContent = 'لطفاً تمامی فیلدها را با فرمت صحیح تکمیل فرمایید.';
                loginAlert.classList.remove('d-none');
                return false;
            }

            // Show spinner animation
            submitBtn.querySelector('.spinner-border').classList.remove('d-none');
            submitBtn.setAttribute('disabled', 'disabled');
        });
    });
</script>
