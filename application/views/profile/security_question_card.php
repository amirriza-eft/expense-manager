<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 mb-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
        style="border-color:var(--border-subtle)!important;">
        سوال امنیتی
    </h5>

    <p class="text-muted small mb-3">
        سوال امنیتی برای بازیابی رمز عبور استفاده می‌شود. پاسخ ذخیره‌شده نمایش داده نمی‌شود.
    </p>

    <div v-if="currentSecurityQuestion" class="alert alert-secondary py-2 px-3 small border-0 mb-3">
        سوال فعلی: {{ currentSecurityQuestion }}
    </div>
    <div v-else class="alert alert-warning py-2 px-3 small border-0 mb-3">
        هنوز سوال امنیتی تنظیم نشده است. برای بازیابی رمز عبور آن را تنظیم کنید.
    </div>

    <div v-if="securityMessage"
         :class="securityMessageSuccess ? 'alert alert-success py-2 px-3 small' : 'alert alert-danger py-2 px-3 small'">
        {{ securityMessage }}
    </div>

    <form @submit.prevent="saveSecurityQuestion">
        <div class="mb-3">
            <label class="form-label text-light small">سوال امنیتی</label>
            <select class="form-select" v-model="securityQuestionId" required>
                <option value="">یک سوال انتخاب کنید</option>
                <option v-for="q in securityQuestions" :key="q.id" :value="String(q.id)">{{ q.text }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label text-light small">پاسخ جدید</label>
            <input type="text" class="form-control" v-model="securityAnswer" required autocomplete="off">
        </div>

        <div class="mb-4">
            <label class="form-label text-light small">رمز عبور فعلی</label>
            <input type="password" class="form-control" v-model="securityCurrentPassword" required>
        </div>

        <button type="submit" class="btn btn-orange-outline" :disabled="securityLoading">
            <span v-if="securityLoading" class="spinner-border spinner-border-sm me-1"></span>
            {{ securityLoading ? 'در حال ذخیره...' : 'ذخیره سوال امنیتی' }}
        </button>
    </form>
</div>
