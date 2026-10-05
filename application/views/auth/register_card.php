<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="register-app" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color:var(--border-subtle)!important;">
    <div class="text-center mb-4">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">ثبت‌نام در حساب‌یار</h3>
        <p class="text-muted small">مدیریت هوشمند درآمدها و هزینه‌های خود را آغاز کنید</p>
    </div>

    <div v-if="error" class="alert alert-danger py-2 px-3 small border-0">{{ error }}</div>
    <div v-if="success" class="alert alert-success py-2 px-3 small border-0">{{ success }}</div>

    <form @submit.prevent="register">
        <div class="mb-3">
            <label class="form-label text-light small fw-medium">نام و نام خانوادگی</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="full_name" placeholder="مثل: بابک زنجانی" v-model="full_name" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-light small fw-medium">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="name@example.com" v-model="email" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-light small fw-medium">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input :type="showPassword ? 'text' : 'password'" class="form-control" name="password" placeholder="حداقل ۸ کاراکتر" v-model="password" required>
                <button type="button" class="input-group-text toggle-password" @click="showPassword = !showPassword">
                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium">تکرار رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                <input :type="showPasswordConfirm ? 'text' : 'password'" class="form-control" name="password_confirm" placeholder="تکرار رمز عبور" v-model="password_confirm" required>
                <button type="button" class="input-group-text toggle-password" @click="showPasswordConfirm = !showPasswordConfirm">
                    <i :class="showPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" :disabled="loading">
            {{ loading ? 'در حال ثبت نام...' : 'ایجاد حساب کاربری' }}
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color:var(--border-subtle)!important;">
        <span class="text-muted small">قبلاً حساب ساخته‌اید؟</span>
        <a href="<?= site_url('login') ?>" class="text-decoration-none small fw-bold text-accent-orange">وارد شوید</a>
    </div>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                full_name: '',
                email: '',
                password: '',
                password_confirm: '',
                showPassword: false,
                showPasswordConfirm: false,
                loading: false,
                error: '',
                success: ''
            };
        },

        methods: {
            async register() {
                this.error = '';
                this.success = '';

                if (this.password !== this.password_confirm) {
                    this.error = 'رمز عبور و تکرار آن یکسان نیست';
                    return;
                }

                this.loading = true;

                try {
                    const formData = new FormData();

                    formData.append('full_name', this.full_name);
                    formData.append('email', this.email);
                    formData.append('password', this.password);
                    formData.append('password_confirm', this.password_confirm);

                    const response = await axios.post(
                        "<?= site_url('api/auth/register') ?>",
                        formData
                    );

                    const data = response.data;

                    if (data.status) {
                        this.success = data.message;

                        setTimeout(() => {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 1000);
                    } else {
                        this.error = data.message;
                    }
                } catch (error) {
                    this.error = 'خطا در ارتباط با سرور';
                } finally {
                    this.loading = false;
                }
            }
        }
    }).mount('#register-app');
</script>