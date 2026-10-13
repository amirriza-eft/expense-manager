<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div v-if="showPasswordRecoveryPanel" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-4">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">بازیابی رمز عبور</h3>
        <p class="text-muted small">با سوال امنیتی حساب خود، رمز عبور را بازنشانی کنید</p>
    </div>

    <div v-if="passwordRecoveryError" class="alert alert-danger py-2 px-3 small border-0">{{ passwordRecoveryError }}</div>
    <div v-if="passwordRecoverySuccess" class="alert alert-success py-2 px-3 small border-0">{{ passwordRecoverySuccess }}</div>

    <form v-if="passwordRecoveryStep === 1" @submit.prevent="loadRecoveryQuestion">
        <div class="mb-4">
            <label class="form-label text-light small fw-medium">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" v-model="email" required>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" :disabled="passwordRecoveryLoading">
            {{ passwordRecoveryLoading ? 'در حال بررسی...' : 'ادامه' }}
        </button>
    </form>

    <form v-else-if="passwordRecoveryStep === 2" @submit.prevent="verifyRecoveryAnswer">
        <div class="mb-3">
            <label class="form-label text-light small fw-medium">سوال امنیتی شما</label>
            <div class="form-control bg-transparent text-light" style="min-height:auto;">{{ recoveryQuestion }}</div>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium">پاسخ</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-question-circle"></i></span>
                <input type="text" class="form-control" v-model="recoveryAnswer" required autocomplete="off">
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" :disabled="passwordRecoveryLoading">
            {{ passwordRecoveryLoading ? 'در حال بررسی...' : 'تأیید پاسخ' }}
        </button>
    </form>

    <form v-else-if="passwordRecoveryStep === 3" @submit.prevent="resetRecoveredPassword">
        <div class="mb-3">
            <label class="form-label text-light small fw-medium">رمز عبور جدید</label>
            <input type="password" class="form-control" v-model="recoveryNewPassword" required minlength="8">
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium">تکرار رمز عبور جدید</label>
            <input type="password" class="form-control" v-model="recoveryNewPasswordConfirm" required minlength="8">
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" :disabled="passwordRecoveryLoading">
            {{ passwordRecoveryLoading ? 'در حال ذخیره...' : 'تغییر رمز عبور' }}
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <button type="button" class="btn btn-link text-decoration-none small fw-bold text-accent-orange p-0" @click="closePasswordRecovery">
            بازگشت به ورود
        </button>
    </div>
</div>
