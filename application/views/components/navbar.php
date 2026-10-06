<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($page_title) ? html_escape($page_title) . ' | حساب‌یار' : 'حساب‌یار - مدیریت هوشمند مالی' ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/star-background.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="<?= base_url('assets/js/format.js') ?>"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<?php $this->load->view('components/star_background'); ?>

<div id="navbar-app" style="display:contents;">
    <nav class="navbar navbar-dark navbar-custom sticky-top">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="<?= site_url('home') ?>">
                <i class="bi bi-wallet2 brand-icon fs-3"></i>
                <span>حساب<span class="brand-accent">‌یار</span></span>
            </a>

            <?php if ($this->session->userdata('user_id')): ?>
                <?php
                $avatar = $this->session->userdata('user_avatar');
                $user_name = $this->session->userdata('user_name')
                        ?: ($this->session->userdata('full_name') ?: 'کاربر');
                ?>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-danger" title="خروج" @click="openLogoutModal">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-sm-inline">خروج</span>
                    </button>

                    <a href="<?= site_url('profile') ?>" class="text-decoration-none" title="پروفایل">
                        <?php $this->load->view('profile/avatar', [
                                'avatar_filename' => $avatar,
                                'user_name' => $user_name,
                                'size' => 38,
                                'css_class' => 'user-nav-avatar',
                                'element_id' => 'navbarAvatar',
                                'alt' => 'آواتار کاربر',
                        ]); ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= site_url('login') ?>" class="btn btn-orange-outline">
                        <i class="bi bi-box-arrow-in-left"></i> ورود
                    </a>
                    <a href="<?= site_url('signup') ?>" class="btn btn-orange-glow">
                        <i class="bi bi-person-plus"></i> ثبت‌نام
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content glass-panel text-white" style="background:#1e1e24;border:1px solid var(--border-subtle);">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-box-arrow-right text-danger fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold mb-2">خروج از حساب</h6>
                    <p class="text-muted small mb-0">آیا مطمئن هستید که می‌خواهید از حساب خود خارج شوید؟</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="logoutLoading">انصراف</button>
                        <button type="button" class="btn btn-danger" @click="logout" :disabled="logoutLoading">
                            <span v-if="logoutLoading" class="spinner-border spinner-border-sm me-1"></span>
                            {{ logoutLoading ? 'در حال خروج...' : 'بله، خارج شو' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="flex-grow-1">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    Vue.createApp({
        data() {
            return {
                logoutLoading: false,
                logoutModal: null
            };
        },
        mounted() {
            const element = document.getElementById('logoutModal');
            if (element) this.logoutModal = new bootstrap.Modal(element);
        },
        methods: {
            openLogoutModal() {
                if (this.logoutModal) this.logoutModal.show();
            },
            async logout() {
                if (this.logoutLoading) return;
                this.logoutLoading = true;

                try {
                    const response = await axios.post("<?= site_url('api/auth/logout') ?>");

                    if (response.data.status) {

                        setTimeout(() => {
                            window.location.href = "<?= site_url('login') ?>";
                        }, 800);

                        return;
                    }

                    this.logoutLoading = false;
                } catch (error) {
                    console.error(error);
                    this.logoutLoading = false;
                }
            }
        }
    }).mount('#navbar-app');
</script>

