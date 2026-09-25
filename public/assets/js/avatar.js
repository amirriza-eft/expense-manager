/**
 * Shared avatar URL helpers and graceful fallbacks.
 */
(function (global) {
    'use strict';

    var AVATAR_BASE = global.APP_AVATAR_BASE || '/uploads/avatars/';

    function fallbackAvatarUrl(name, size) {
        size = size || 100;
        var initial = (name || 'ک').trim().charAt(0) || 'ک';
        return 'https://placehold.co/' + size + 'x' + size + '/1e1e24/ff6b00?text=' + encodeURIComponent(initial);
    }

    function resolveAvatarUrl(filename, name, size) {
        if (filename) {
            return AVATAR_BASE.replace(/\/?$/, '/') + filename;
        }
        return fallbackAvatarUrl(name, size);
    }

    function bindAvatarFallback(img, name, size) {
        if (!img) {
            return;
        }

        img.addEventListener('error', function onError() {
            img.removeEventListener('error', onError);
            img.src = fallbackAvatarUrl(name, size);
        });
    }

    function setAvatar(img, filename, name, size) {
        if (!img) {
            return;
        }
        img.src = resolveAvatarUrl(filename, name, size);
        bindAvatarFallback(img, name, size);
    }

    global.fallbackAvatarUrl = fallbackAvatarUrl;
    global.resolveAvatarUrl = resolveAvatarUrl;
    global.bindAvatarFallback = bindAvatarFallback;
    global.setAvatar = setAvatar;
})(window);
