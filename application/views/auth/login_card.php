<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="login-app">

    <div v-if="!showRecoveryPanel && !showPasswordRecoveryPanel" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">

        <div class="text-center mb-5">
            <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
                <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
            </a>
            <h3 class="fw-bold text-white mb-2">ورود به حساب‌یار</h3>
            <p class="text-muted small">برای دسترسی به پنل مدیریت مالی وارد حساب خود شوید</p>
        </div>

        <div v-if="error" class="alert alert-danger py-2 px-3 small border-0">{{ error }}</div>
        <div v-if="success" class="alert alert-success py-2 px-3 small border-0">{{ success }}</div>
        <div v-if="deletedAccount" class="alert alert-danger py-2 px-3 small border-0">
            <div class="mb-2">{{ deletedMessage }}</div>
            <button type="button" class="btn btn-orange-outline btn-sm" @click="showRecovery">
                <i class="bi bi-arrow-counterclockwise"></i> بازیابی حساب
            </button>
        </div>

        <form @submit.prevent="login">
            <div class="mb-3">
                <label class="form-label text-light small fw-medium" for="email">ایمیل</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" v-model="email" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-light small fw-medium" for="password">رمز عبور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input :type="showPassword ? 'text' : 'password'" class="form-control" name="password" id="password" placeholder="••••••••" v-model="password" required>
                    <button class="input-group-text toggle-password" type="button" @click="showPassword = !showPassword">
                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div class="form-check remember-wrapper">
                    <input type="checkbox" value="1" id="rememberMe" name="remember_me" v-model="rememberMe">
                    <label for="rememberMe">مرا به خاطر بسپار</label>
                </div>

                <button type="button" class="btn btn-link text-decoration-none small fw-bold text-accent-orange p-0"
                        @click="openPasswordRecovery">
                    فراموشی رمز عبور؟
                </button>
            </div>

            <button type="submit" class="btn btn-orange-glow w-100" :disabled="loading">
                {{ loading ? 'در حال ورود...' : 'ورود به حساب' }}
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
            <span class="text-muted small">حساب کاربری ندارید؟</span>
            <a href="<?= site_url('signup') ?>" class="text-decoration-none small fw-bold text-accent-orange">ثبت‌نام کنید</a>
        </div>

    </div>

    <?php $this->load->view('auth/account_recovery_card'); ?>
    <?php $this->load->view('auth/password_recovery_card'); ?>

</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                email: '',
                password: '',
                rememberMe: false,
                showPassword: false,
                loading: false,
                error: '',
                success: '',
                deletedAccount: false,
                deletedMessage: '',

                showRecoveryPanel: false,
                showRecoveryPassword: false,
                recoveryLoading: false,
                recoveryError: '',
                recoverySuccess: '',

                showPasswordRecoveryPanel: false,
                passwordRecoveryStep: 1,
                passwordRecoveryLoading: false,
                passwordRecoveryError: '',
                passwordRecoverySuccess: '',
                recoveryQuestion: '',
                recoveryAnswer: '',
                recoveryNewPassword: '',
                recoveryNewPasswordConfirm: ''
            };
        },

        methods: {
            async login() {
                this.error = '';
                this.success = '';
                this.deletedAccount = false;
                this.deletedMessage = '';
                this.loading = true;

                try {
                    const formData = new FormData();

                    formData.append('email', this.email);
                    formData.append('password', this.password);
                    formData.append('remember_me', this.rememberMe ? 1 : 0);

                    const response = await axios.post(
                        "<?= site_url('api/auth/login') ?>",
                        formData
                    );

                    const data = response.data;

                    if (data.status) {
                        this.success = data.message;

                        setTimeout(() => {
                            window.location.href = "<?= site_url('home') ?>";
                        }, 800);

                        return;
                    }

                    if (data.code === 'account_deleted') {
                        this.deletedAccount = true;
                        this.deletedMessage = data.message;
                        return;
                    }

                    this.error = data.message;

                } catch (error) {
                    this.error = 'خطا در ارتباط با سرور';

                } finally {
                    this.loading = false;
                }
            },

            showRecovery() {
                this.showRecoveryPanel = true;
                this.showPasswordRecoveryPanel = false;
                this.deletedAccount = false;
            },

            openPasswordRecovery() {
                this.showPasswordRecoveryPanel = true;
                this.showRecoveryPanel = false;
                this.passwordRecoveryStep = 1;
                this.passwordRecoveryError = '';
                this.passwordRecoverySuccess = '';
                this.recoveryQuestion = '';
                this.recoveryAnswer = '';
                this.recoveryNewPassword = '';
                this.recoveryNewPasswordConfirm = '';
            },

            closePasswordRecovery() {
                this.showPasswordRecoveryPanel = false;
                this.passwordRecoveryStep = 1;
                this.passwordRecoveryError = '';
                this.passwordRecoverySuccess = '';
            },

            async loadRecoveryQuestion() {
                this.passwordRecoveryError = '';
                this.passwordRecoverySuccess = '';
                this.passwordRecoveryLoading = true;

                try {
                    const formData = new FormData();
                    formData.append('email', this.email);

                    const response = await axios.post(
                        "<?= site_url('api/auth/recovery/question') ?>",
                        formData
                    );

                    if (response.data.status) {
                        this.recoveryQuestion = response.data.question;
                        this.passwordRecoveryStep = 2;
                    } else {
                        this.passwordRecoveryError = response.data.message;
                    }
                } catch (e) {
                    this.passwordRecoveryError = 'خطا در ارتباط با سرور';
                } finally {
                    this.passwordRecoveryLoading = false;
                }
            },

            async verifyRecoveryAnswer() {
                this.passwordRecoveryError = '';
                this.passwordRecoverySuccess = '';
                this.passwordRecoveryLoading = true;

                try {
                    const formData = new FormData();
                    formData.append('email', this.email);
                    formData.append('security_answer', this.recoveryAnswer);

                    const response = await axios.post(
                        "<?= site_url('api/auth/recovery/verify') ?>",
                        formData
                    );

                    if (response.data.status) {
                        this.passwordRecoverySuccess = response.data.message;
                        this.passwordRecoveryStep = 3;
                        this.recoveryAnswer = '';
                    } else {
                        this.passwordRecoveryError = response.data.message;
                    }
                } catch (e) {
                    this.passwordRecoveryError = 'خطا در ارتباط با سرور';
                } finally {
                    this.passwordRecoveryLoading = false;
                }
            },

            async resetRecoveredPassword() {
                this.passwordRecoveryError = '';
                this.passwordRecoverySuccess = '';
                this.passwordRecoveryLoading = true;

                try {
                    const formData = new FormData();
                    formData.append('new_password', this.recoveryNewPassword);
                    formData.append('new_password_confirm', this.recoveryNewPasswordConfirm);

                    const response = await axios.post(
                        "<?= site_url('api/auth/recovery/reset') ?>",
                        formData
                    );

                    if (response.data.status) {
                        this.passwordRecoverySuccess = response.data.message;
                        setTimeout(() => {
                            this.closePasswordRecovery();
                            this.success = 'رمز عبور تغییر کرد. وارد شوید.';
                        }, 1200);
                    } else {
                        this.passwordRecoveryError = response.data.message;
                    }
                } catch (error) {
                    if (error.response && error.response.data && error.response.data.message) {
                        this.passwordRecoveryError = error.response.data.message;
                    } else {
                        this.passwordRecoveryError = 'خطا در ارتباط با سرور';
                    }
                } finally {
                    this.passwordRecoveryLoading = false;
                }
            },

            async restoreAccount() {

                this.recoveryError = '';
                this.recoverySuccess = '';
                this.recoveryLoading = true;

                let formData = new FormData();

                formData.append('email', this.email);
                formData.append('password', this.password);

                try {

                    let response = await axios.post(
                        "<?= site_url('api/auth/restore') ?>",
                        formData
                    );

                    let data = response.data;

                    if (data.status) {

                        this.recoverySuccess = data.message;

                        setTimeout(() => {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 1200);

                        return;
                    }
                    this.recoveryError = data.message;

                } catch (e) {
                    this.recoveryError = 'خطا در ارتباط با سرور';

                } finally {
                    this.recoveryLoading = false;
                }
            }
        }
    }).mount('#login-app');
</script>
