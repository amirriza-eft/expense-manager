/**
 * Display-layer number formatting (Persian digits via fa-IR locale).
 * Do not use for API payloads, form values, IDs, or URLs.
 */
(function (global) {
    'use strict';

    function toNumber(value) {
        if (value === null || value === undefined || value === '') {
            return 0;
        }
        var n = Number(value);
        return Number.isFinite(n) ? n : 0;
    }

    function formatNumber(value, options) {
        return toNumber(value).toLocaleString('fa-IR', options || {});
    }

    function formatAmount(value, type) {
        var sign = type === 'income' ? '+' : type === 'expense' ? '-' : '';
        return sign + formatNumber(value);
    }

    global.formatNumber = formatNumber;
    global.formatAmount = formatAmount;
})(window);
