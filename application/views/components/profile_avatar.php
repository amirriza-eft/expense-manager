<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
    $avatar_filename = $avatar_filename ?? null;
    $user_name = $user_name ?? 'کاربر';
    $size = (int) ($size ?? 100);
    $css_class = $css_class ?? 'rounded-circle border';
    $element_id = $element_id ?? '';
    $alt = $alt ?? 'آواتار';

    $initial = mb_substr(trim((string) $user_name), 0, 1);
    if ($initial === '') {
        $initial = 'ک';
    }

    $fallback_url = 'https://placehold.co/' . $size . 'x' . $size . '/1e1e24/ff6b00?text=' . rawurlencode($initial);
    $avatar_url = !empty($avatar_filename)
        ? base_url('uploads/avatars/' . $avatar_filename)
        : $fallback_url;
?>

<img
    src="<?= html_escape($avatar_url) ?>"
    alt="<?= html_escape($alt) ?>"
    class="<?= html_escape($css_class) ?>"
    <?php if ($element_id !== ''): ?>id="<?= html_escape($element_id) ?>"<?php endif; ?>
    data-avatar-name="<?= html_escape($user_name) ?>"
    data-avatar-size="<?= $size ?>"
    style="width:<?= $size ?>px;height:<?= $size ?>px;object-fit:cover;border-color:var(--accent-orange)!important;background-color:var(--bg-surface);"
    onerror="this.onerror=null;this.src='<?= html_escape($fallback_url) ?>';"
>
