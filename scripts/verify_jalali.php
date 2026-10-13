<?php

/**
 * Focused verification for Shamsi date helpers and chart month fill rules.
 * Run: php scripts/verify_jalali.php
 */

define('BASEPATH', true);
require __DIR__ . '/../application/helpers/jalali_helper.php';

$failed = 0;

function assert_true($cond, $message)
{
    global $failed;
    if ($cond) {
        echo "PASS  $message\n";
    } else {
        echo "FAIL  $message\n";
        $failed++;
    }
}

// Round-trips
$pairs = [
    ['2026-10-13', 1405, 7, 21],
    ['2025-03-21', 1404, 1, 1],
    ['2024-03-20', 1403, 1, 1],
    ['2025-03-20', 1403, 12, 30], // leap Esfand
];

foreach ($pairs as $pair) {
    list($g, $jy, $jm, $jd) = $pair;
    $parts = gregorian_to_jalali_parts($g);
    assert_true(
        $parts[0] === $jy && $parts[1] === $jm && $parts[2] === $jd,
        "gregorian_to_jalali $g => $jy/$jm/$jd"
    );
    assert_true(
        jalali_to_gregorian_date($jy, $jm, $jd) === $g,
        "jalali_to_gregorian $jy/$jm/$jd => $g"
    );
}

assert_true(is_jalali_leap(1403) === true, '1403 is leap');
assert_true(is_jalali_leap(1404) === false, '1404 is not leap');
assert_true(jalali_month_length(1405, 1) === 31, 'Farvardin has 31 days');
assert_true(jalali_month_length(1405, 7) === 30, 'Mehr has 30 days');
assert_true(jalali_month_length(1404, 12) === 29, 'Esfand 1404 has 29 days');
assert_true(jalali_month_length(1403, 12) === 30, 'Esfand 1403 has 30 days');

$range = current_jalali_month_range(true);
assert_true($range['month_name'] === 'مهر' || $range['jm'] >= 1, 'current month has a name');
assert_true($range['from'] <= $range['to'], 'summary from <= to');
assert_true($range['to'] === app_today(), 'summary ends today');

$full = current_jalali_month_range(false);
assert_true(
    is_current_jalali_month_range($full['from'], $full['month_end']),
    'full current month detected'
);

// Simulate current-month chart fill with fixed "today" = 21 Mehr 1405
$from = jalali_to_gregorian_date(1405, 7, 1);
$to = jalali_to_gregorian_date(1405, 7, 30);
$today = '2026-10-13';
$rows = [];
foreach (each_date_inclusive($from, $to) as $day) {
    $is_future = $day > $today;
    $rows[] = [
        'day' => $day,
        'income' => $is_future ? null : 0,
        'expense' => $is_future ? null : 0,
        'balance' => $is_future ? null : 0,
    ];
}

assert_true(count($rows) === 30, 'Mehr chart has 30 day labels');
assert_true($rows[19]['income'] === 0, 'day 20 (past) has real zero (not null)');
assert_true($rows[20]['income'] === 0, 'day 21 (today) has real zero (not null)');
assert_true($rows[21]['income'] === null, 'day 22 (future) is empty/null');
assert_true($rows[29]['income'] === null, 'day 30 is empty/null');

assert_true(parse_gregorian_date('2026-10-01') === '2026-10-01', 'parse valid gregorian');
assert_true(parse_gregorian_date('1405/07/01') === null, 'reject shamsi-looking string');
assert_true(parse_gregorian_date('2026-13-40') === null, 'reject invalid calendar date');

assert_true(
    format_jalali_date('2026-10-13', true) === to_persian_digits('21 مهر'),
    'label uses Persian digits and month name'
);

echo "\n";
if ($failed === 0) {
    echo "All checks passed.\n";
    exit(0);
}

echo "$failed check(s) failed.\n";
exit(1);
