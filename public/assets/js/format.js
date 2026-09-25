/**
 * Display-layer formatting helpers.
 * Numbers/dates for UI only — never use for API payloads, IDs, or URLs.
 *
 * Date conversion mirrors the search filters' persian-date approach.
 */
(function (global) {
    'use strict';

    var PERSIAN_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
    var ENGLISH_DIGITS = '0123456789';

    function toNumber(value) {
        if (value === null || value === undefined || value === '') {
            return 0;
        }
        var n = Number(value);
        return Number.isFinite(n) ? n : 0;
    }

    function toPersianDigits(value) {
        return String(value == null ? '' : value).replace(/\d/g, function (digit) {
            return PERSIAN_DIGITS[digit];
        });
    }

    function toEnglishDigits(value) {
        return String(value == null ? '' : value).replace(/[۰-۹]/g, function (char) {
            return ENGLISH_DIGITS[PERSIAN_DIGITS.indexOf(char)];
        });
    }

    function formatNumber(value, options) {
        return toNumber(value).toLocaleString('fa-IR', options || {});
    }

    function formatAmount(value, type) {
        var sign = type === 'income' ? '+' : type === 'expense' ? '-' : '';
        return sign + formatNumber(value);
    }

    /**
     * Convert Shamsi date from picker (YYYY/MM/DD) to Gregorian YYYY-MM-DD for APIs.
     * Same logic as the search filters.
     */
    function convertPersianToGregorian(date) {
        if (!date) {
            return '';
        }

        if (typeof persianDate === 'undefined') {
            return '';
        }

        date = toEnglishDigits(String(date).trim());

        var parts = date.split(/[\/\-]/);
        if (parts.length < 3) {
            return '';
        }

        var pDate = new persianDate([
            Number(parts[0]),
            Number(parts[1]),
            Number(parts[2])
        ]);

        return toEnglishDigits(pDate.toCalendar('gregorian').format('YYYY-MM-DD'));
    }

    /**
     * Format a Gregorian date (YYYY-MM-DD or datetime) for display as Shamsi YYYY/MM/DD.
     * Uses the same persian-date library as the search section.
     */
    function formatPersianDate(date) {
        if (!date) {
            return '';
        }

        var raw = toEnglishDigits(String(date).trim()).substring(0, 10);
        var parts = raw.split(/[\/\-]/);

        if (parts.length < 3) {
            return toPersianDigits(date);
        }

        var year = Number(parts[0]);
        var month = Number(parts[1]);
        var day = Number(parts[2]);

        if (!year || !month || !day) {
            return toPersianDigits(date);
        }

        if (typeof persianDate === 'undefined') {
            return toPersianDigits(raw.replace(/-/g, '/'));
        }

        // Build from Gregorian Date — same library family as search filters
        var formatted = new persianDate(new Date(year, month - 1, day)).format('YYYY/MM/DD');
        return toPersianDigits(toEnglishDigits(formatted));
    }

    /** Local calendar YYYY-MM-DD (not UTC) for form defaults. */
    function todayGregorianDate() {
        var d = new Date();
        var month = String(d.getMonth() + 1);
        var day = String(d.getDate());
        if (month.length < 2) {
            month = '0' + month;
        }
        if (day.length < 2) {
            day = '0' + day;
        }
        return d.getFullYear() + '-' + month + '-' + day;
    }

    global.toPersianDigits = toPersianDigits;
    global.toEnglishDigits = toEnglishDigits;
    global.formatNumber = formatNumber;
    global.formatAmount = formatAmount;
    global.convertPersianToGregorian = convertPersianToGregorian;
    global.formatPersianDate = formatPersianDate;
    global.todayGregorianDate = todayGregorianDate;
})(window);
