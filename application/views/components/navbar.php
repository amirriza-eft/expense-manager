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
            --text-muted: #b3b3bc;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-orange: rgba(255, 107, 0, 0.5);
            --font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
            --radius-control: 0.75rem;
            --control-height: 2.75rem;
            --btn-font-size: 0.9rem;
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

        .text-muted {
            color: var(--text-muted) !important;
        }

        .glass-panel {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-subtle);
            border-radius: 1.25rem;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
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
            transition: color 0.25s ease, background 0.25s ease;
        }

        .nav-link i {
            vertical-align: -0.1em;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-orange) !important;
            background: rgba(255, 107, 0, 0.08);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }

        .btn {
            font-size: var(--btn-font-size);
            font-weight: 600;
            line-height: 1.35;
            min-height: var(--control-height);
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-control);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .btn-sm {
            min-height: 2.15rem;
            padding: 0.35rem 0.8rem;
            font-size: 0.82rem;
        }

        .btn-orange-glow {
            background: linear-gradient(135deg, var(--accent-orange) 0%, var(--accent-orange-hover) 100%);
            border: none;
            color: #ffffff !important;
            box-shadow: 0 3px 14px var(--accent-glow);
        }

        .btn-orange-glow:hover {
            box-shadow: 0 4px 18px rgba(255, 107, 0, 0.45);
            filter: brightness(1.06);
            color: #ffffff !important;
        }

        .btn-orange-glow:focus-visible,
        .btn-orange-outline:focus-visible {
            outline: 2px solid var(--accent-orange);
            outline-offset: 2px;
        }

        .btn-orange-glow:disabled,
        .btn-orange-glow.disabled {
            opacity: 0.65;
            box-shadow: none;
            filter: none;
        }

        .btn-orange-outline {
            border: 1.5px solid var(--accent-orange);
            color: var(--accent-orange) !important;
            background: transparent;
        }

        .btn-orange-outline:hover {
            background: rgba(255, 107, 0, 0.12);
            color: var(--accent-orange-hover) !important;
            box-shadow: 0 0 12px rgba(255, 107, 0, 0.18);
        }

        .btn-secondary {
            background-color: #2a2a35 !important;
            border-color: #3d3d4e !important;
            color: var(--text-main) !important;
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: #353544 !important;
            border-color: #4a4a5c !important;
            color: #ffffff !important;
        }

        .btn-outline-secondary {
            border-color: #4a4a5c !important;
            color: var(--text-muted) !important;
        }

        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: #6a6a7c !important;
            color: var(--text-main) !important;
        }

        .btn-outline-danger {
            border-color: rgba(239, 68, 68, 0.55) !important;
            color: #f87171 !important;
        }

        .btn-outline-danger:hover,
        .btn-outline-danger:focus {
            background-color: rgba(239, 68, 68, 0.15) !important;
            border-color: var(--expense-red) !important;
            color: #fecaca !important;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-group-sm > .btn {
            min-height: 2rem;
            padding: 0.3rem 0.55rem;
            font-size: 0.8rem;
        }

        .form-label {
            margin-bottom: 0.45rem;
        }

        .form-control, .form-select, .form-check-input {
            background-color: var(--bg-input) !important;
            border: 1px solid #3d3d4e !important;
            color: var(--text-main) !important;
            border-radius: var(--radius-control);
            min-height: var(--control-height);
            padding: 0.55rem 0.95rem;
            font-size: 0.95rem;
        }

        textarea.form-control {
            min-height: auto;
        }

        .form-control::placeholder {
            color: #8a8a9a;
            opacity: 1;
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23b3b3bc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }

        .form-select option {
            background-color: var(--bg-surface);
            color: var(--text-main);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.22) !important;
            outline: none;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.72);
            cursor: pointer;
            opacity: 0.85;
        }

        /* Fully rounded input groups — avoids pointy/mismatched corners in RTL */
        .input-group {
            border: 1px solid #3d3d4e;
            border-radius: var(--radius-control);
            overflow: hidden;
            background-color: var(--bg-input);
            min-height: var(--control-height);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group:focus-within {
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.22);
        }

        .input-group > .form-control,
        .input-group > .form-select,
        .input-group > .input-group-text,
        .input-group > button.input-group-text {
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            min-height: calc(var(--control-height) - 2px);
            background-color: transparent !important;
        }

        .input-group-text {
            color: var(--text-muted);
            padding: 0.55rem 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        button.input-group-text {
            cursor: pointer;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        button.input-group-text:hover {
            color: var(--accent-orange);
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        .user-nav-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-orange);
            background-color: var(--bg-surface);
        }

        .badge-income,
        .badge-expense {
            font-weight: 500;
            padding: 0.35em 0.7em;
            border-radius: 0.4rem;
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

        .badge.bg-secondary {
            background-color: #3a3a48 !important;
            color: #d4d4dc !important;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border: 1px solid rgba(16, 185, 129, 0.28) !important;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
            border: 1px solid rgba(239, 68, 68, 0.28) !important;
        }

        .modal-content.glass-panel {
            background: var(--bg-surface) !important;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.45);
        }

        .modal-header,
        .modal-footer {
            border-color: var(--border-subtle) !important;
        }

        .table-dark {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            --bs-table-border-color: var(--border-subtle);
            --bs-table-striped-bg: transparent;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.04);
            --bs-table-hover-color: var(--text-main);
        }

        .table-dark > :not(caption) > * > * {
            background-color: transparent;
            border-bottom-color: var(--border-subtle);
            box-shadow: none;
        }

        .table thead th {
            font-weight: 600;
            white-space: nowrap;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .table td {
            vertical-align: middle;
        }

        .list-group-item {
            background-color: transparent;
        }

        .hover-orange {
            transition: color 0.2s ease;
        }

        .hover-orange:hover {
            color: var(--accent-orange) !important;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #2b2b36; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-orange); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="<?= site_url('home') ?>">
            <i class="bi bi-wallet2 brand-icon fs-3"></i>
            <span>حساب<span style="color: var(--accent-orange);">‌یار</span></span>
        </a>

        <?php if ($this->session->userdata('user_id')): ?>
            <?php
            $avatar = $this->session->userdata('user_avatar');
            $user_name = $this->session->userdata('user_name') ?? 'کاربر';
            $avatar_url = !empty($avatar)
                ? base_url('uploads/avatars/' . $avatar)
                : 'https://placehold.co/100x100/1e1e24/ff6b00?text=' . urlencode(mb_substr($user_name, 0, 1));
            ?>
            <!-- Avatar (left) + logout (right of avatar) -->
            <div class="d-flex align-items-center gap-3">
                <a href="#" id="logoutBtn" class="btn btn-outline-danger" title="خروج">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">خروج</span>
                </a>
                <a href="<?= site_url('profile') ?>" class="text-decoration-none" title="پروفایل">
                    <img src="<?= $avatar_url ?>" alt="Avatar" class="user-nav-avatar">
                </a>
            </div>
        <?php else: ?>
            <!-- Guest Actions -->
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

    document.getElementById('logoutBtn')
        ?.addEventListener('click', function(e){
            e.preventDefault();

            fetch("<?= site_url('api/auth/logout') ?>", {
                method:"POST"
            })
                .then(response => response.json())

                .then(data => {
                    if(data.status)
                    {
                        window.location.href =
                            "<?= site_url('login') ?>";
                    }
                })

                .catch(error => {
                    console.log(error);
                });
        });
</script>