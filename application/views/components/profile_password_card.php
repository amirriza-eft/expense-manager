<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2" style="border-color: var(--border-subtle) !important;">
        تغییر کلمه عبور
    </h5>

    <form action="<?= site_url('profile/change_password') ?>" method="POST">
        <div class="mb-3">
            <label for="current_password" class="form-label text-light small fw-medium">رمز عبور فعلی</label>
            <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="••••••••">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="new_password" class="form-label text-light small fw-medium">رمز عبور جدید</label>
                <input type="password" class="form-control" name="new_password" id="new_password" minlength="6" required placeholder="حداقل ۶ کاراکتر">
            </div>
            <div class="col-md-6">
                <label for="new_password_confirm" class="form-label text-light small fw-medium">تکرار رمز عبور جدید</label>
                <input type="password" class="form-control" name="new_password_confirm" id="new_password_confirm" required placeholder="تکرار رمز عبور جدید">
            </div>
        </div>

        <button type="submit" class="btn btn-orange-outline px-4 py-2">
            به‌روزرسانی رمز عبور
        </button>
    </form>
</div>

