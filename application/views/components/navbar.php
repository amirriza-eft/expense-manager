<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($page_title) ? html_escape($page_title) . ' | حساب‌یار' : 'حساب‌یار - مدیریت هوشمند مالی' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/navbar/style.css') ?>">

    <script>
        window.APP_AVATAR_BASE = "<?= base_url('uploads/avatars/') ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="<?= base_url('assets/js/format.js') ?>"></script>
    <script src="<?= base_url('assets/js/avatar.js') ?>"></script>
</head>
<body>

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
                <a href="#" id="logoutBtn" class="btn btn-outline-danger" title="خروج">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">خروج</span>
                </a>
                <a href="<?= site_url('profile') ?>" class="text-decoration-none" title="پروفایل">
                    <?php $this->load->view('components/profile_avatar', [
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

<main class="flex-grow-1">

<script>
    document.getElementById('logoutBtn')?.addEventListener('click', function (e) {
        e.preventDefault();

        fetch("<?= site_url('api/auth/logout') ?>", { method: 'POST' })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.status) {
                    window.location.href = "<?= site_url('login') ?>";
                }
            })
            .catch(function (error) {
                console.error(error);
            });
    });
</script>
