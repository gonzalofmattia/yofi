/**
 * Wishlist — Yofi (localStorage + sync con servidor al loguearse)
 */
const WISHLIST_STORAGE_KEY = 'yofi_wishlist';

function getWishlistIds() {
    try {
        var raw = localStorage.getItem(WISHLIST_STORAGE_KEY);
        var parsed = raw ? JSON.parse(raw) : [];
        if (!Array.isArray(parsed)) return [];
        return parsed.map(function (id) { return String(parseInt(id, 10)); }).filter(function (id) { return id !== '0' && id !== 'NaN'; });
    } catch (e) {
        return [];
    }
}

function saveWishlistIds(ids) {
    var unique = [];
    ids.forEach(function (id) {
        var s = String(parseInt(id, 10));
        if (s !== '0' && s !== 'NaN' && unique.indexOf(s) === -1) unique.push(s);
    });
    try {
        localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(unique));
    } catch (e) {}
    updateWishlistUi();
    document.dispatchEvent(new CustomEvent('wishlist:updated', { detail: { ids: unique } }));
    return unique;
}

function isInWishlist(productId) {
    return getWishlistIds().indexOf(String(parseInt(productId, 10))) >= 0;
}

function toggleWishlistLocal(productId) {
    var id = String(parseInt(productId, 10));
    var ids = getWishlistIds();
    var idx = ids.indexOf(id);
    var added;
    if (idx >= 0) {
        ids.splice(idx, 1);
        added = false;
    } else {
        ids.push(id);
        added = true;
    }
    saveWishlistIds(ids);
    return { added: added, ids: ids };
}

function updateWishlistUi() {
    var ids = getWishlistIds();
    document.querySelectorAll('[data-action="wishlist-toggle"]').forEach(function (btn) {
        var article = btn.closest('[data-product-id]');
        var pid = article ? article.getAttribute('data-product-id') : btn.getAttribute('data-product-id');
        if (!pid) return;
        var active = ids.indexOf(String(parseInt(pid, 10))) >= 0;
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        btn.classList.toggle('text-accent', active);
        btn.classList.toggle('opacity-100', active);
        var svg = btn.querySelector('svg');
        if (svg) {
            if (active) {
                svg.setAttribute('fill', 'currentColor');
            } else {
                svg.setAttribute('fill', 'none');
            }
        }
    });

    var badge = document.querySelector('[data-wishlist-count]');
    if (badge) {
        if (ids.length > 0) {
            badge.textContent = String(ids.length);
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }
}

function apiFetch(url, options) {
    options = options || {};
    options.headers = options.headers || {};
    if (window.YOFI && window.YOFI.csrfToken) {
        options.headers['X-CSRF-Token'] = window.YOFI.csrfToken;
    }
    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }
    return fetch(url, options).then(function (r) { return r.json(); });
}

function syncWishlistWithServer() {
    if (!window.YOFI || !window.YOFI.loggedIn || !window.YOFI.apiWishlistSync) {
        return Promise.resolve(getWishlistIds());
    }
    return apiFetch(window.YOFI.apiWishlistSync, {
        method: 'POST',
        body: { product_ids: getWishlistIds().map(function (id) { return parseInt(id, 10); }) }
    }).then(function (data) {
        if (data && data.success && Array.isArray(data.product_ids)) {
            saveWishlistIds(data.product_ids.map(String));
        }
        return getWishlistIds();
    }).catch(function () {
        return getWishlistIds();
    });
}

function toggleWishlist(productId) {
    var id = parseInt(productId, 10);
    if (!id) return Promise.resolve({ added: false });

    if (window.YOFI && window.YOFI.loggedIn && window.YOFI.apiWishlistToggle) {
        return apiFetch(window.YOFI.apiWishlistToggle, {
            method: 'POST',
            body: { product_id: id }
        }).then(function (data) {
            var local = getWishlistIds();
            var sid = String(id);
            if (data && data.success) {
                if (data.in_wishlist) {
                    if (local.indexOf(sid) < 0) local.push(sid);
                } else {
                    local = local.filter(function (x) { return x !== sid; });
                }
                saveWishlistIds(local);
                return { added: !!data.in_wishlist };
            }
            return toggleWishlistLocal(id);
        }).catch(function () {
            return toggleWishlistLocal(id);
        });
    }

    return Promise.resolve(toggleWishlistLocal(id));
}

function initWishlistButtons() {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-action="wishlist-toggle"]');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        var article = btn.closest('[data-product-id]');
        var pid = article ? article.getAttribute('data-product-id') : null;
        if (!pid) return;
        toggleWishlist(pid);
    });

    var headerBtn = document.querySelector('[data-wishlist-trigger]');
    if (headerBtn) {
        headerBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var base = window.YOFI && window.YOFI.pageMiCuenta ? window.YOFI.pageMiCuenta : 'index.php?p=mi-cuenta';
            window.location.href = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'tab=deseos';
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updateWishlistUi();
    initWishlistButtons();
    if (window.YOFI && window.YOFI.loggedIn) {
        syncWishlistWithServer();
    }
});

window.YofiWishlist = {
    STORAGE_KEY: WISHLIST_STORAGE_KEY,
    getWishlistIds: getWishlistIds,
    saveWishlistIds: saveWishlistIds,
    isInWishlist: isInWishlist,
    toggleWishlist: toggleWishlist,
    syncWishlistWithServer: syncWishlistWithServer,
    updateWishlistUi: updateWishlistUi
};
