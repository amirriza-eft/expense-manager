<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$label = $label ?? '';
$icon = $icon ?? 'bi-circle';
$icon_style = $icon_style ?? '';
$amount = $amount ?? 0;
$amount_id = $amount_id ?? '';
$prefix = $prefix ?? '';
$amount_class = $amount_class ?? 'text-white';
$amount_style = $amount_style ?? '';
$subtitle = $subtitle ?? '';
?>

<div class="col-12 col-sm-6 col-lg-3">
    <div class="glass-panel p-3 h-100 position-relative">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium"><?= html_escape($label) ?></span>
            <i class="bi <?= html_escape($icon) ?> fs-4"
               <?php if ($icon_style !== ''): ?>style="<?= html_escape($icon_style) ?>"<?php endif; ?>></i>
        </div>
        <h4 class="fw-bold mb-1 <?= html_escape($amount_class) ?>"
            <?php if ($amount_id !== ''): ?>id="<?= html_escape($amount_id) ?>"<?php endif; ?>
            <?php if ($amount_style !== ''): ?>style="<?= html_escape($amount_style) ?>"<?php endif; ?>>
            <?= html_escape($prefix) ?><?= format_number($amount) ?>
            <span class="fs-6 text-muted">تومان</span>
        </h4>
        <?php if ($subtitle !== ''): ?>
            <div class="small text-muted"><?= html_escape($subtitle) ?></div>
        <?php endif; ?>
    </div>
</div>
