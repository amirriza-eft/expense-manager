<?php
defined('BASEPATH') or exit('No direct script access allowed');

function format_number($value, $decimals = 0)
{
    $number = is_numeric($value) ? (float)$value : 0.0;
    $formatted = number_format($number, $decimals);

    return to_persian_digits($formatted);
}

function to_persian_digits($value)
{
    return strtr((string)$value, [
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
