(function (global) {
    'use strict';

    let AVATAR_BASE = global.APP_AVATAR_BASE || '/uploads/avatars/';

    function fallbackAvatarUrl(name, size) {
        size = size || 100;
        let initial = (name || 'ک').trim().charAt(0) || 'ک';
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

        if (img._avatarErrorHandler) {
            img.removeEventListener('error', img._avatarErrorHandler);
        }

        img._avatarErrorHandler = function onError() {
            img.removeEventListener('error', img._avatarErrorHandler);
            img._avatarErrorHandler = null;
            img.src = fallbackAvatarUrl(name, size);
        };

        img.addEventListener('error', img._avatarErrorHandler);
    }

    function setAvatar(img, filename, name, size) {
        if (!img) {
            return;
        }
        img.src = resolveAvatarUrl(filename, name, size);
        bindAvatarFallback(img, name, size);
    }

    global.setAvatar = setAvatar;
})(window);
