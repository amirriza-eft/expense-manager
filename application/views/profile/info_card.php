<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="profile-app">
    <div class="glass-panel p-4 mb-4">
        <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
            style="border-color:var(--border-subtle)!important;">
            اطلاعات کاربری و تصویر پروفایل
        </h5>

        <div v-if="profileMessage"
             :class="profileMessageSuccess ? 'alert alert-success py-2 px-3 small' : 'alert alert-danger py-2 px-3 small'">
            {{ profileMessage }}
        </div>

        <form @submit.prevent="updateProfile" enctype="multipart/form-data">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
                <div class="profile-avatar-wrap position-relative">
                    <?php
                    $session_name = $this->session->userdata('user_name')
                            ?: ($this->session->userdata('full_name') ?: 'کاربر');
                    $this->load->view('profile/avatar', [
                            'avatar_filename' => $this->session->userdata('user_avatar'),
                            'user_name' => $session_name,
                            'size' => 100,
                            'css_class' => 'rounded-circle border profile-avatar-preview',
                            'element_id' => 'avatarPreview',
                            'alt' => 'آواتار پروفایل',
                    ]);
                    ?>

                    <div v-if="profileLoading"
                         class="profile-avatar-loading"
                         aria-hidden="true">
                        <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                    </div>
                </div>

                <div class="flex-grow-1 w-100">
                    <label class="form-label small text-muted" for="avatarInput">تصویر پروفایل</label>
                    <input
                            type="file"
                            name="avatar"
                            id="avatarInput"
                            class="form-control"
                            accept="image/png,image/jpeg,image/webp"
                            @change="previewAvatar"
                            ref="avatarInput"
                    >
                    <small class="text-muted">JPG PNG WEBP - حداکثر ۲ مگابایت</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-light small">نام و نام خانوادگی</label>
                <input
                        type="text"
                        class="form-control"
                        name="full_name"
                        v-model="fullName"
                        required
                >
            </div>

            <div class="mb-4">
                <label class="form-label text-light small">ایمیل</label>
                <input
                        type="email"
                        class="form-control"
                        name="email"
                        v-model="email"
                        required
                >
            </div>

            <button type="submit" class="btn btn-orange-glow" :disabled="profileLoading">
                <span v-if="profileLoading" class="spinner-border spinner-border-sm me-1"></span>
                {{ profileLoading ? 'در حال ذخیره...' : 'ذخیره اطلاعات' }}
            </button>
        </form>
    </div>

    <?php $this->load->view('profile/password_card'); ?>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                fullName: '',
                email: '',
                profileLoading: false,
                profileMessage: '',
                profileMessageSuccess: false,
                currentPassword: '',
                newPassword: '',
                newPasswordConfirm: '',
                passwordLoading: false,
                passwordMessage: '',
                passwordMessageSuccess: false,
                deletePassword: '',
                deleteLoading: false,
                deleteMessage: '',
                deleteMessageSuccess: false
            };
        },

        mounted() {
            this.loadProfile();
        },

        methods: {

            async loadProfile() {
                try {
                    const { data } = await axios.get("<?= site_url('api/profile') ?>");

                    if (!data.status || !data.user) return;

                    this.fullName = data.user.full_name || '';
                    this.email = data.user.email || '';

                    this.updateAvatar(
                        this.$refs.avatarPreview,
                        data.user.avatar,
                        data.user.full_name,
                        100
                    );

                    this.updateAvatar(
                        document.getElementById('navbarAvatar'),
                        data.user.avatar,
                        data.user.full_name,
                        38
                    );
                } catch (error) {
                    console.error(error);
                }
            },

            updateAvatar(element, avatar, name, size) {
                if (!element) return;

                if (avatar) {
                    element.src = "<?= base_url('uploads/avatars/') ?>" + avatar;
                    return;
                }

                const initial = (name || 'کاربر').trim().charAt(0);

                const fallbackUrl =
                    'https://placehold.co/' +
                    size + 'x' + size +
                    '/1e1e24/ff6b00?text=' +
                    encodeURIComponent(initial || 'ک');

                element.src = fallbackUrl;
            },

            previewAvatar(event) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();

                reader.onload = event => {
                    this.$refs.avatarPreview.src = event.target.result;
                };

                reader.readAsDataURL(file);
            },

            async updateProfile() {
                this.profileLoading = true;
                this.profileMessage = '';

                try {
                    const formData = new FormData();

                    formData.append('full_name', this.fullName);
                    formData.append('email', this.email);

                    const file = this.$refs.avatarInput.files[0];
                    if (file) formData.append('avatar', file);

                    const { data } = await axios.post(
                        "<?= site_url('api/profile/update') ?>",
                        formData
                    );

                    this.profileMessage = data.message || '';
                    this.profileMessageSuccess = !!data.status;

                    if (data.status && data.user) {
                        this.updateAvatar(
                            this.$refs.avatarPreview,
                            data.user.avatar,
                            data.user.full_name,
                            100
                        );

                        this.updateAvatar(
                            document.getElementById('navbarAvatar'),
                            data.user.avatar,
                            data.user.full_name,
                            38
                        );
                    }
                } catch (error) {
                    this.profileMessage = 'خطا در ارتباط با سرور';
                    this.profileMessageSuccess = false;
                } finally {
                    this.profileLoading = false;
                }
            },

            async changePassword() {
                this.passwordLoading = true;
                this.passwordMessage = '';

                try {
                    const formData = new FormData();

                    formData.append('current_password', this.currentPassword);
                    formData.append('new_password', this.newPassword);
                    formData.append('new_password_confirm', this.newPasswordConfirm);

                    const { data } = await axios.post(
                        "<?= site_url('api/profile/password') ?>",
                        formData
                    );

                    this.passwordMessage = data.message || '';
                    this.passwordMessageSuccess = !!data.status;

                    if (data.status) {
                        this.currentPassword = '';
                        this.newPassword = '';
                        this.newPasswordConfirm = '';
                    }
                } catch (error) {
                    this.passwordMessage = 'خطا در ارتباط با سرور';
                    this.passwordMessageSuccess = false;
                } finally {
                    this.passwordLoading = false;
                }
            },

            async deleteAccount() {
                if (!this.deletePassword) {
                    this.deleteMessage = 'لطفاً رمز عبور خود را وارد کنید';
                    this.deleteMessageSuccess = false;
                    return;
                }

                this.deleteLoading = true;
                this.deleteMessage = '';

                try {
                    const formData = new URLSearchParams({
                        password: this.deletePassword
                    });

                    const { data } = await axios.post(
                        "<?= site_url('api/user/delete') ?>",
                        formData
                    );

                    this.deleteMessage = data.message || '';
                    this.deleteMessageSuccess = !!data.status;

                    if (data.status) {
                        setTimeout(() => {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 1500);
                    }
                } catch (error) {
                    this.deleteMessage = 'خطا در ارتباط با سرور';
                    this.deleteMessageSuccess = false;
                } finally {
                    this.deleteLoading = false;
                }
            }
        }
    }).mount('#profile-app');
</script>
