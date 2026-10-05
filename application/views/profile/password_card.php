<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
        style="border-color:var(--border-subtle)!important;">
        تغییر کلمه عبور
    </h5>

    <div v-if="passwordMessage"
         :class="passwordMessageSuccess ? 'alert alert-success py-2 px-3 small' : 'alert alert-danger py-2 px-3 small'">
        {{ passwordMessage }}
    </div>

    <form @submit.prevent="changePassword">
        <div class="mb-3">
            <label class="form-label text-light small">رمز عبور فعلی</label>
            <input
                    type="password"
                    class="form-control"
                    name="current_password"
                    v-model="currentPassword"
                    required
            >
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-light small">رمز عبور جدید</label>
                <input
                        type="password"
                        class="form-control"
                        name="new_password"
                        v-model="newPassword"
                        required
                >
            </div>

            <div class="col-md-6">
                <label class="form-label text-light small">تکرار رمز عبور جدید</label>
                <input
                        type="password"
                        class="form-control"
                        name="new_password_confirm"
                        v-model="newPasswordConfirm"
                        required
                >
            </div>
        </div>

        <button type="submit" class="btn btn-orange-outline" :disabled="passwordLoading">
            <span v-if="passwordLoading" class="spinner-border spinner-border-sm me-1"></span>
            {{ passwordLoading ? 'در حال تغییر...' : 'تغییر رمز عبور' }}
        </button>
    </form>
</div>

<div class="glass-panel p-4 mt-4">
    <h5 class="fw-bold text-white mb-3">حذف حساب کاربری</h5>

    <p class="text-muted small mb-3">
        با حذف حساب، اطلاعات شما ابتدا غیرفعال می‌شود و پس از ۳۰ روز به‌صورت کامل حذف خواهد شد.
    </p>

    <button
            type="button"
            class="btn btn-outline-danger btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#deleteAccountModal"
    >
        <i class="bi bi-person-x"></i>
        حذف حساب کاربری
    </button>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-panel text-white"
             style="background:#1e1e24;border:1px solid var(--border-subtle);">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">حذف حساب کاربری</h5>
                <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>

                <p class="text-muted small">
                    با حذف حساب، حساب شما غیرفعال می‌شود.
                    <br>
                    تا ۳۰ روز امکان بازیابی حساب وجود دارد.
                    <br>
                    پس از آن اطلاعات به‌صورت کامل حذف خواهد شد.
                </p>

                <div v-if="deleteMessage"
                     :class="deleteMessageSuccess ? 'alert alert-success py-2 small' : 'alert alert-danger py-2 small'">
                    {{ deleteMessage }}
                </div>

                <input
                        type="password"
                        class="form-control mb-3"
                        placeholder="رمز عبور"
                        v-model="deletePassword"
                >

                <div class="d-flex justify-content-center gap-2">
                    <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            :disabled="deleteLoading">
                        انصراف
                    </button>

                    <button
                            type="button"
                            class="btn btn-danger"
                            @click="deleteAccount"
                            :disabled="deleteLoading">
                        <span v-if="deleteLoading" class="spinner-border spinner-border-sm me-1"></span>
                        {{ deleteLoading ? 'در حال حذف...' : 'حذف حساب' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

