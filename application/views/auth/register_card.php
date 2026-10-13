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

        <div class="mb-3">
            <label class="form-label text-light small fw-medium">تکرار رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                <input :type="showPasswordConfirm ? 'text' : 'password'" class="form-control" name="password_confirm" placeholder="تکرار رمز عبور" v-model="password_confirm" required>
                <button type="button" class="input-group-text toggle-password" @click="showPasswordConfirm = !showPasswordConfirm">
                    <i :class="showPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-light small fw-medium">سوال امنیتی</label>
            <select class="form-select" name="security_question_id" v-model="security_question_id" required>
                <option value="">یک سوال انتخاب کنید</option>
                <option v-for="(text, id) in securityQuestions" :key="id" :value="String(id)">{{ text }}</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium">پاسخ سوال امنیتی</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-question-circle"></i></span>
                <input type="text" class="form-control" name="security_answer" placeholder="پاسخ خود را وارد کنید" v-model="security_answer" required autocomplete="off">
            </div>
            <small class="text-muted">این پاسخ برای بازیابی رمز عبور استفاده می‌شود</small>
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
                security_question_id: '',
                security_answer: '',
                securityQuestions: <?= json_encode(security_questions_list(), JSON_UNESCAPED_UNICODE) ?>,
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

                if (!this.full_name.trim()) {
                    this.error = 'وارد کردن نام و نام خانوادگی الزامی است.';
                    return;
                }

                if (this.password !== this.password_confirm) {
                    this.error = 'رمز عبور و تکرار آن یکسان نیست';
                    return;
                }

                if (!this.security_question_id) {
                    this.error = 'انتخاب سوال امنیتی الزامی است';
                    return;
                }

                if (!this.security_answer.trim()) {
                    this.error = 'پاسخ سوال امنیتی الزامی است';
                    return;
                }

                this.loading = true;

                try {
                    const formData = new FormData();

                    formData.append('full_name', this.full_name.trim());
                    formData.append('email', this.email.trim());
                    formData.append('password', this.password);
                    formData.append('password_confirm', this.password_confirm);
                    formData.append('security_question_id', this.security_question_id);
                    formData.append('security_answer', this.security_answer);

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
                    if (error.response && error.response.data && error.response.data.message) {
                        this.error = error.response.data.message;
                    } else {
                        this.error = 'خطا در ارتباط با سرور';
                    }
                } finally {
                    this.loading = false;
                }
            }
        }
    }).mount('#register-app');
</script>
