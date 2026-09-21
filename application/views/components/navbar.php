<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($page_title) ? html_escape($page_title) . ' | حساب‌یار' : 'حساب‌یار - مدیریت هوشمند مالی' ?></title>

    <!-- Google Fonts: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL CSS & Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-body: #121212;
            --bg-surface: #1e1e24;
            --bg-card: rgba(30, 30, 36, 0.75);
            --bg-input: #25252d;
            --accent-orange: #ff6b00;
            --accent-orange-hover: #ff7a18;
            --accent-glow: rgba(255, 107, 0, 0.35);
            --income-green: #10b981;
            --expense-red: #ef4444;
            --text-main: #f8f9fa;
            --text-muted: #a0a0a0;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-orange: rgba(255, 107, 0, 0.5);
            --font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .glass-panel {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-subtle);
            border-radius: 1.25rem;
        }

        .navbar-custom {
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.85rem 0;
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.45rem;
            color: var(--text-main) !important;
            letter-spacing: -0.5px;
        }

        .brand-icon {
            color: var(--accent-orange);
            filter: drop-shadow(0 0 10px var(--accent-glow));
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.25s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-orange) !important;
            background: rgba(255, 107, 0, 0.08);
        }

        .btn-orange-glow {
            background: linear-gradient(135deg, var(--accent-orange) 0%, var(--accent-orange-hover) 100%);
            border: none;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 4px 18px var(--accent-glow);
            transition: all 0.3s ease;
            border-radius: 0.65rem;
        }

        .btn-orange-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 107, 0, 0.5);
            filter: brightness(1.08);
        }

        .btn-orange-outline {
            border: 1.5px solid var(--accent-orange);
            color: var(--accent-orange) !important;
            background: transparent;
            font-weight: 600;
            border-radius: 0.65rem;
            transition: all 0.3s ease;
        }

        .btn-orange-outline:hover {
            background: rgba(255, 107, 0, 0.12);
            color: var(--accent-orange-hover) !important;
            box-shadow: 0 0 15px rgba(255, 107, 0, 0.2);
            transform: translateY(-2px);
        }

        .form-control, .form-select, .form-check-input {
            background-color: var(--bg-input) !important;
            border: 1px solid #3d3d4e !important;
            color: var(--text-main) !important;
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .form-control::placeholder { color: #6c757d; }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 0, 0.25) !important;
            outline: none;
        }

        .input-group-text {
            background-color: var(--bg-input);
            border: 1px solid #3d3d4e;
            color: var(--text-muted);
            border-radius: 0.65rem;
        }

        .user-nav-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-orange);
        }

        .badge-income {
            background: rgba(16, 185, 129, 0.15);
            color: var(--income-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-expense {
            background: rgba(239, 68, 68, 0.15);
            color: var(--expense-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #2b2b36; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-orange); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('home') ?>">
            <i class="bi bi-wallet2 brand-icon fs-3"></i>
            <span>حساب<span style="color: var(--accent-orange);">‌یار</span></span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <i class="bi bi-list fs-2 text-light"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <?php if ($this->session->userdata('user_id')): ?>
                <!-- Authenticated Nav Links -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 pe-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'home' || uri_string() == '') ? 'active' : '' ?>" href="<?= site_url('home') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> داشبورد
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#categoryManagerModal">
                            <i class="bi bi-tags me-1"></i> مدیریت دسته‌بندی‌ها
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'profile') ? 'active' : '' ?>" href="<?= site_url('profile') ?>">
                            <i class="bi bi-person-gear me-1"></i> پروفایل
                        </a>
                    </li>
                </ul>

                <!-- Authenticated User Menu -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <a href="<?= site_url('profile') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                        <?php
                        $avatar = $this->session->userdata('user_avatar');
                        $user_name = $this->session->userdata('user_name') ?? 'کاربر';
                        $avatar_url = !empty($avatar)
                            ? base_url('uploads/avatars/' . $avatar)
                            : 'https://placehold.co/100x100/1e1e24/ff6b00?text=' . urlencode(mb_substr($user_name, 0, 1));
                        ?>
                        <img src="<?= $avatar_url ?>" alt="Avatar" class="user-nav-avatar">
                        <span class="text-light small fw-medium d-none d-md-inline"><?= html_escape($user_name) ?></span>
                    </a>
                    <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm px-3 py-1 rounded-2" title="خروج">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-sm-inline ms-1">خروج</span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Guest Actions -->
                <div class="d-flex align-items-center gap-2 ms-auto mt-3 mt-lg-0">
                    <a href="<?= site_url('login') ?>" class="btn btn-orange-outline px-3 py-2">
                        <i class="bi bi-box-arrow-in-left ms-1"></i> ورود
                    </a>
                    <a href="<?= site_url('signup') ?>" class="btn btn-orange-glow px-3 py-2">
                        <i class="bi bi-person-plus ms-1"></i> ثبت‌نام
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
