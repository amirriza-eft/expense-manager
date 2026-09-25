<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 mb-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
        style="border-color:var(--border-subtle)!important;">
        اطلاعات کاربری و تصویر پروفایل
    </h5>

    <div id="profileMessage" class="alert d-none py-2 px-3 small"></div>

    <form id="profileForm" enctype="multipart/form-data">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
            <div class="profile-avatar-wrap position-relative">
                <?php
                $session_name = $this->session->userdata('user_name')
                    ?: ($this->session->userdata('full_name') ?: 'کاربر');
                $this->load->view('components/profile_avatar', [
                    'avatar_filename' => $this->session->userdata('user_avatar'),
                    'user_name' => $session_name,
                    'size' => 100,
                    'css_class' => 'rounded-circle border profile-avatar-preview',
                    'element_id' => 'avatarPreview',
                    'alt' => 'آواتار پروفایل',
                ]);
                ?>
                <div id="avatarLoading"
                     class="profile-avatar-loading d-none"
                     aria-hidden="true">
                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                </div>
            </div>

            <div class="flex-grow-1 w-100">
                <label class="form-label small text-muted" for="avatarInput">
                    تصویر پروفایل
                </label>
                <input
                    type="file"
                    name="avatar"
                    id="avatarInput"
                    class="form-control"
                    accept="image/png,image/jpeg,image/webp"
                >
                <small class="text-muted">JPG PNG WEBP - حداکثر ۲ مگابایت</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-light small" for="full_name">نام و نام خانوادگی</label>
            <input type="text" class="form-control" name="full_name" id="full_name" required>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small" for="email">ایمیل</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <button type="submit" class="btn btn-orange-glow" id="profileSubmitBtn">
            ذخیره اطلاعات
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var preview = document.getElementById('avatarPreview');
        var loading = document.getElementById('avatarLoading');
        var messageBox = document.getElementById('profileMessage');
        var submitBtn = document.getElementById('profileSubmitBtn');
        var avatarBase = "<?= base_url('uploads/avatars/') ?>";

        function showMessage(ok, text) {
            messageBox.classList.remove('d-none');
            messageBox.className = ok
                ? 'alert alert-success py-2 px-3 small'
                : 'alert alert-danger py-2 px-3 small';
            messageBox.textContent = text;
        }

        function setLoading(isLoading) {
            loading.classList.toggle('d-none', !isLoading);
        }

        function loadProfile() {
            setLoading(true);

            fetch("<?= site_url('api/profile') ?>")
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.status || !data.user) {
                        return;
                    }

                    var user = data.user;
                    document.getElementById('full_name').value = user.full_name || '';
                    document.getElementById('email').value = user.email || '';

                    if (typeof setAvatar === 'function') {
                        setAvatar(preview, user.avatar, user.full_name, 100);
                    } else if (user.avatar) {
                        preview.src = avatarBase + user.avatar;
                    }

                    var navbarAvatar = document.getElementById('navbarAvatar');
                    if (navbarAvatar && typeof setAvatar === 'function') {
                        setAvatar(navbarAvatar, user.avatar, user.full_name, 38);
                    }
                })
                .catch(function (error) {
                    console.error(error);
                })
                .finally(function () {
                    setLoading(false);
                });
        }

        document.getElementById('avatarInput').addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('profileForm').addEventListener('submit', function (e) {
            e.preventDefault();

            submitBtn.disabled = true;
            setLoading(true);

            fetch("<?= site_url('api/profile/update') ?>", {
                method: 'POST',
                body: new FormData(this)
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    showMessage(!!data.status, data.message || '');

                    if (!data.status || !data.user) {
                        return;
                    }

                    var user = data.user;

                    if (typeof setAvatar === 'function') {
                        setAvatar(preview, user.avatar, user.full_name, 100);
                    }

                    var navbarAvatar = document.getElementById('navbarAvatar');
                    if (navbarAvatar && typeof setAvatar === 'function') {
                        setAvatar(navbarAvatar, user.avatar, user.full_name, 38);
                    }
                })
                .catch(function () {
                    showMessage(false, 'خطا در ارتباط با سرور');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    setLoading(false);
                });
        });

        loadProfile();
    });
</script>
