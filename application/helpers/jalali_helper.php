<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Shamsi / Jalali calendar helpers.
 * Gregorian dates stay in the database; convert only at the edges.
 */

if (!function_exists('jalali_month_names')) {
    function jalali_month_names()
    {
        return [
            1  => 'فروردین',
            2  => 'اردیبهشت',
            3  => 'خرداد',
            4  => 'تیر',
            5  => 'مرداد',
            6  => 'شهریور',
            7  => 'مهر',
            8  => 'آبان',
            9  => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
    }
}

if (!function_exists('jalali_month_name')) {
    function jalali_month_name($month)
    {
        $names = jalali_month_names();
        $month = (int) $month;

        return $names[$month] ?? '';
    }
}

if (!function_exists('to_persian_digits')) {
    function to_persian_digits($value)
    {
        return strtr((string) $value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }
}

if (!function_exists('to_english_digits')) {
    function to_english_digits($value)
    {
        return strtr((string) $value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
}

if (!function_exists('gregorian_to_jalali')) {
    /**
     * @return array{0:int,1:int,2:int} [year, month, day]
     */
    function gregorian_to_jalali($gy, $gm, $gd)
    {
        $gy = (int) $gy;
        $gm = (int) $gm;
        $gd = (int) $gd;

        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4)
            - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400)
            + $gd + $g_d_m[$gm - 1];

        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }
}

if (!function_exists('jalali_to_gregorian')) {
    /**
     * @return array{0:int,1:int,2:int} [year, month, day]
     */
    function jalali_to_gregorian($jy, $jm, $jd)
    {
        $jy = (int) $jy;
        $jm = (int) $jm;
        $jd = (int) $jd;

        $jy += 1595;
        $days = -355668 + (365 * $jy) + (intdiv($jy, 33) * 8)
            + intdiv(($jy % 33) + 3, 4) + $jd
            + (($jm < 7) ? (($jm - 1) * 31) : ((($jm - 7) * 30) + 186));

        $gy = 400 * intdiv($days, 146097);
        $days %= 146097;

        if ($days > 36524) {
            $gy += 100 * intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }

        $gy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $gy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $gd = $days + 1;
        $sal_a = [0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28,
            31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        for ($gm = 1; $gm <= 12 && $gd > $sal_a[$gm]; $gm++) {
            $gd -= $sal_a[$gm];
        }

        return [$gy, $gm, $gd];
    }
}

if (!function_exists('is_jalali_leap')) {
    /**
     * Leap if 30 Esfand is a valid day for that Jalali year.
     */
    function is_jalali_leap($jy)
    {
        list($gy, $gm, $gd) = jalali_to_gregorian((int) $jy, 12, 30);
        list($jy2, $jm2, $jd2) = gregorian_to_jalali($gy, $gm, $gd);

        return $jy2 === (int) $jy && $jm2 === 12 && $jd2 === 30;
    }
}

if (!function_exists('jalali_month_length')) {
    function jalali_month_length($jy, $jm)
    {
        $jm = (int) $jm;

        if ($jm <= 6) {
            return 31;
        }

        if ($jm <= 11) {
            return 30;
        }

        return is_jalali_leap($jy) ? 30 : 29;
    }
}

if (!function_exists('format_ymd')) {
    function format_ymd($y, $m, $d)
    {
        return sprintf('%04d-%02d-%02d', (int) $y, (int) $m, (int) $d);
    }
}

if (!function_exists('jalali_to_gregorian_date')) {
    /** @return string YYYY-MM-DD */
    function jalali_to_gregorian_date($jy, $jm, $jd)
    {
        list($gy, $gm, $gd) = jalali_to_gregorian($jy, $jm, $jd);

        return format_ymd($gy, $gm, $gd);
    }
}

if (!function_exists('gregorian_to_jalali_parts')) {
    /**
     * @param string $date YYYY-MM-DD (or datetime)
     * @return array{0:int,1:int,2:int}|null
     */
    function gregorian_to_jalali_parts($date)
    {
        $date = to_english_digits(substr(trim((string) $date), 0, 10));
        $parts = preg_split('/[\/\-]/', $date);

        if (!$parts || count($parts) < 3) {
            return null;
        }

        return gregorian_to_jalali((int) $parts[0], (int) $parts[1], (int) $parts[2]);
    }
}

if (!function_exists('format_jalali_date')) {
    /**
     * Format Gregorian YYYY-MM-DD as Shamsi YYYY/MM/DD with Persian digits.
     */
    function format_jalali_date($gregorian_date, $with_month_name = false)
    {
        $parts = gregorian_to_jalali_parts($gregorian_date);

        if (!$parts) {
            return '';
        }

        list($jy, $jm, $jd) = $parts;

        if ($with_month_name) {
            $label = $jd . ' ' . jalali_month_name($jm);

            return to_persian_digits($label);
        }

        return to_persian_digits(sprintf('%04d/%02d/%02d', $jy, $jm, $jd));
    }
}

if (!function_exists('parse_gregorian_date')) {
    /**
     * Normalize a Gregorian date string to YYYY-MM-DD, or null if invalid.
     */
    function parse_gregorian_date($date)
    {
        $date = to_english_digits(trim((string) $date));
        $date = substr($date, 0, 10);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }

        list($y, $m, $d) = array_map('intval', explode('-', $date));

        if (!checkdate($m, $d, $y)) {
            return null;
        }

        return format_ymd($y, $m, $d);
    }
}

if (!function_exists('app_today')) {
    /** Today's date in the app timezone as YYYY-MM-DD. */
    function app_today()
    {
        return date('Y-m-d');
    }
}

if (!function_exists('current_jalali_month_range')) {
    /**
     * Current Shamsi month as Gregorian DATE bounds.
     *
     * @param bool $through_today When true, end is today (for summary).
     *                            When false, end is last day of the Shamsi month (for chart axis).
     * @return array{from:string,to:string,jy:int,jm:int,month_name:string,label:string}
     */
    function current_jalali_month_range($through_today = true)
    {
        $today = app_today();
        list($jy, $jm, $jd) = gregorian_to_jalali_parts($today);

        $from = jalali_to_gregorian_date($jy, $jm, 1);
        $month_end = jalali_to_gregorian_date($jy, $jm, jalali_month_length($jy, $jm));
        $to = $through_today ? $today : $month_end;

        $month_name = jalali_month_name($jm);

        return [
            'from'       => $from,
            'to'         => $to,
            'month_end'  => $month_end,
            'jy'         => $jy,
            'jm'         => $jm,
            'jd'         => $jd,
            'month_name' => $month_name,
            'label'      => $month_name . ' ' . to_persian_digits($jy),
        ];
    }
}

if (!function_exists('is_current_jalali_month_range')) {
    /**
     * True when from/to equal the full current Shamsi month (day 1 .. last day).
     */
    function is_current_jalali_month_range($from, $to)
    {
        $range = current_jalali_month_range(false);

        return $from === $range['from'] && $to === $range['month_end'];
    }
}

if (!function_exists('each_date_inclusive')) {
    /**
     * Yield each Gregorian YYYY-MM-DD from $from through $to inclusive.
     *
     * @return Generator|string[]
     */
    function each_date_inclusive($from, $to)
    {
        $current = strtotime($from . ' 00:00:00');
        $end = strtotime($to . ' 00:00:00');

        if ($current === false || $end === false) {
            return;
        }

        while ($current <= $end) {
            yield date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
    }
}
