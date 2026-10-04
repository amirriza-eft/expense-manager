<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="login-app" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
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

<?php $this->load->view('components/account_recovery_card'); ?>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
                deletedMessage: ''
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
                if (typeof window.showRecoveryPanel === 'function') {
                    window.showRecoveryPanel(this.email, this.password);
                }
            }
        }
    }).mount('#login-app');
</script>