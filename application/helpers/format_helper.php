<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Format a number for display with Persian (fa-IR) digits.
 * Presentation only — do not use for machine-readable values.
 *
 * @param mixed $value
 * @param int   $decimals
 * @return string
 */
function format_number($value, $decimals = 0)
{
    $number = is_numeric($value) ? (float) $value : 0.0;
    $formatted = number_format($number, $decimals);

    return to_persian_digits($formatted);
}

/**
 * Replace English digits with Persian digits in a string.
 *
 * @param mixed $value
 * @return string
 */
function to_persian_digits($value)
{
    return strtr((string) $value, [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ]);
}
