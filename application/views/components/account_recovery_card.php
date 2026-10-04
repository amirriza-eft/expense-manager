<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div v-if="showRecoveryPanel" class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color: var(--border-subtle) !important;">
    <div class="text-center mb-5">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>
        <h3 class="fw-bold text-white mb-2">بازیابی حساب</h3>
        <p class="text-muted small">ایمیل و رمز عبور حساب حذف‌شده خود را وارد کنید</p>
    </div>

    <div v-if="recoveryError" class="alert alert-danger py-2 px-3 small border-0">{{ recoveryError }}</div>
    <div v-if="recoverySuccess" class="alert alert-success py-2 px-3 small border-0">{{ recoverySuccess }}</div>

    <form @submit.prevent="restoreAccount">

        <div class="mb-3">
            <label class="form-label text-light small fw-medium">ایمیل</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" v-model="email" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small fw-medium">رمز عبور</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input :type="showRecoveryPassword ? 'text' : 'password'" class="form-control" v-model="password" required>
                <button type="button" class="input-group-text" @click="showRecoveryPassword=!showRecoveryPassword">
                    <i :class="showRecoveryPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-orange-glow w-100" :disabled="recoveryLoading">
            {{ recoveryLoading ? 'در حال بازیابی...' : 'بازیابی حساب' }}
        </button>

    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
        <button type="button" class="btn btn-link text-decoration-none small fw-bold text-accent-orange p-0" @click="showRecoveryPanel=false">
            بازگشت به ورود
        </button>
    </div>
</div>