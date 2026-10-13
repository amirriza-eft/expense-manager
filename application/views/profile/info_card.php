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
                    <img :src="avatarUrl" alt="آواتار پروفایل" class="rounded-circle border profile-avatar-preview"
                         style="width:100px;height:100px;object-fit:cover;border-color:var(--accent-orange)!important;background-color:var(--bg-surface);">

                    <div v-if="profileLoading" class="profile-avatar-loading" aria-hidden="true">
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
                <input type="text" class="form-control" name="full_name" v-model="fullName" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-light small">ایمیل</label>
                <input type="email" class="form-control" name="email" v-model="email" required>
            </div>

            <button type="submit" class="btn btn-orange-glow" :disabled="profileLoading">
                <span v-if="profileLoading" class="spinner-border spinner-border-sm me-1"></span>
                {{ profileLoading ? 'در حال ذخیره...' : 'ذخیره اطلاعات' }}
            </button>
        </form>
    </div>

    <?php $this->load->view('profile/security_question_card'); ?>
    <?php $this->load->view('profile/password_card'); ?>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                avatarUrl: "<?= base_url('assets/img/default-avatar.png') ?>",
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
                deleteMessageSuccess: false,

                securityQuestions: {},
                securityQuestionId: '',
                securityAnswer: '',
                securityCurrentPassword: '',
                currentSecurityQuestion: '',
                securityLoading: false,
                securityMessage: '',
                securityMessageSuccess: false
            };
        },

        mounted() {
            this.loadProfile();
        },

        methods: {

            setAvatar(user) {
                if (user.avatar) {
                    this.avatarUrl = "<?= base_url('uploads/avatars/') ?>" + user.avatar;
                } else {
                    this.avatarUrl = "<?= base_url('assets/img/default-avatar.png') ?>";
                }

                document.getElementById('navbarAvatar').src = this.avatarUrl;
            },

            async loadProfile() {
                const response = await axios.get("<?= site_url('api/profile') ?>");

                if (response.data.status) {
                    this.fullName = response.data.user.full_name;
                    this.email = response.data.user.email;
                    this.setAvatar(response.data.user);

                    this.securityQuestions = response.data.security_questions || {};
                    this.currentSecurityQuestion = response.data.user.security_question || '';
                    this.securityQuestionId = response.data.user.security_question_id
                        ? String(response.data.user.security_question_id)
                        : '';
                    this.securityAnswer = '';
                    this.securityCurrentPassword = '';
                }
            },

            async saveSecurityQuestion() {
                this.securityLoading = true;
                this.securityMessage = '';

                try {
                    const formData = new FormData();
                    formData.append('security_question_id', this.securityQuestionId);
                    formData.append('security_answer', this.securityAnswer);
                    formData.append('current_password', this.securityCurrentPassword);

                    const response = await axios.post(
                        "<?= site_url('api/profile/security-question') ?>",
                        formData
                    );

                    this.securityMessage = response.data.message || '';
                    this.securityMessageSuccess = response.data.status;

                    if (response.data.status) {
                        this.currentSecurityQuestion = response.data.security_question || '';
                        this.securityQuestionId = response.data.security_question_id
                            ? String(response.data.security_question_id)
                            : this.securityQuestionId;
                        this.securityAnswer = '';
                        this.securityCurrentPassword = '';
                    }
                } catch (error) {
                    this.securityMessage = this.apiErrorMessage(
                        error,
                        'خطا در ذخیره سوال امنیتی'
                    );
                    this.securityMessageSuccess = false;
                } finally {
                    this.securityLoading = false;
                }
            },

            previewAvatar(event) {
                const file = event.target.files[0];

                if (file) {
                    this.avatarUrl = URL.createObjectURL(file);
                }
            },

            apiErrorMessage(error, fallback) {
                if (error.response && error.response.data) {
                    const message = error.response.data.message;

                    if (typeof message === 'string' && message.trim()) {
                        return message;
                    }
                }

                return fallback;
            },

            async updateProfile() {
                this.profileLoading = true;
                this.profileMessage = '';

                try {
                    const formData = new FormData();

                    formData.append('full_name', this.fullName.trim());
                    formData.append('email', this.email.trim());

                    const file = this.$refs.avatarInput.files[0];
                    if (file) formData.append('avatar', file);

                    const { data } = await axios.post(
                        "<?= site_url('api/profile/update') ?>",
                        formData
                    );

                    this.profileMessage = data.message || '';
                    this.profileMessageSuccess = data.status;

                    if (data.status && data.user) {
                        this.setAvatar(data.user);
                    }
                } catch (error) {
                    this.profileMessage = this.apiErrorMessage(
                        error,
                        'خطا در ارتباط با سرور'
                    );
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
                    this.passwordMessageSuccess = data.status;

                    if (data.status) {
                        this.currentPassword = '';
                        this.newPassword = '';
                        this.newPasswordConfirm = '';
                    }
                } catch (error) {
                    this.passwordMessage = this.apiErrorMessage(
                        error,
                        'خطا در ارتباط با سرور'
                    );
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
                    const formData = new FormData();
                    formData.append('password', this.deletePassword);

                    const { data } = await axios.post(
                        "<?= site_url('api/user/delete') ?>",
                        formData
                    );

                    this.deleteMessage = data.message || '';
                    this.deleteMessageSuccess = data.status;

                    if (data.status) {
                        setTimeout(() => {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 1500);
                    }
                } catch (error) {
                    this.deleteMessage = this.apiErrorMessage(
                        error,
                        'خطا در ارتباط با سرور'
                    );
                    this.deleteMessageSuccess = false;
                } finally {
                    this.deleteLoading = false;
                }
            }
        }
    }).mount('#profile-app');
</script>