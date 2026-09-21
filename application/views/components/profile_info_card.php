<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 mb-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2" style="border-color: var(--border-subtle) !important;">
        اطلاعات کاربری و تصویر پروفایل
    </h5>

    <form action="<?= site_url('profile/update') ?>" method="POST" enctype="multipart/form-data">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
            <div>
                <?php
                $avatar_url = !empty($user->avatar)
                    ? base_url('uploads/avatars/' . $user->avatar)
                    : 'https://placehold.co/120x120/1e1e24/ff6b00?text=' . urlencode(mb_substr($user->full_name ?? 'ک', 0, 1));
                ?>
                <img src="<?= $avatar_url ?>" id="avatarPreview" alt="Profile Avatar" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover; border-color: var(--accent-orange) !important;">
            </div>
            <div class="flex-grow-1 text-center text-sm-start">
                <label for="avatarInput" class="form-label small text-muted mb-1">بارگذاری تصویر جدید</label>
                <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/webp">
                <div class="text-muted small mt-1" style="font-size: 0.8rem;">فرمت‌های مجاز: JPG, PNG, WEBP (حداکثر ۲ مگابایت)</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label text-light small fw-medium">نام و نام خانوادگی</label>
            <input type="text" class="form-control" name="full_name" id="full_name" value="<?= html_escape($user->full_name ?? '') ?>" required>
        </div>

        <div class="mb-4">
            <label for="email" class="form-label text-light small fw-medium">آدرس ایمیل</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= html_escape($user->email ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-orange-glow px-4 py-2">
            ذخیره اطلاعات
        </button>
    </form>
</div>

<script>
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('avatarPreview').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>