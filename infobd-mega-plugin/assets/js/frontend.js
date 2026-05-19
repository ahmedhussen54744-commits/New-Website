/**
 * Infobd Mega — Frontend JS
 * Reader tools, dark mode, font sizing, reading mode, image zoom, like, bookmark, copy.
 */
(function () {
    'use strict';

    var O = (window.InfobdMega && InfobdMega.opts) || {};
    var I18N = (window.InfobdMega && InfobdMega.i18n) || {};
    var ajaxUrl = (window.InfobdMega && InfobdMega.ajax) || '';
    var nonce = (window.InfobdMega && InfobdMega.nonce) || '';

    document.addEventListener('DOMContentLoaded', function () {

        /* Reader toolbar */
        var rt = document.getElementById('infobd-reader-tools');
        if (rt) {
            rt.querySelector('.irt-toggle').addEventListener('click', function () { rt.classList.toggle('open'); });
            rt.querySelectorAll('button[data-action]').forEach(function (b) {
                b.addEventListener('click', function () {
                    var a = b.dataset.action;
                    if (a === 'dark') toggleDark();
                    else if (a === 'font-up') changeFont(1);
                    else if (a === 'font-down') changeFont(-1);
                    else if (a === 'font-reset') resetFont();
                    else if (a === 'reading') document.body.classList.toggle('infobd-reading-mode');
                    else if (a === 'print') window.print();
                });
            });
        }

        /* Dark mode persistence */
        function toggleDark() {
            var on = document.documentElement.classList.toggle('infobd-dark');
            try { localStorage.setItem('infobd_dark', on ? '1' : '0'); } catch (e) { }
        }
        try {
            if (localStorage.getItem('infobd_dark') === '1') {
                document.documentElement.classList.add('infobd-dark');
            }
        } catch (e) { }

        /* Font size */
        function changeFont(d) {
            var s = parseInt(localStorage.getItem('infobd_font') || '0', 10) + d;
            s = Math.max(-3, Math.min(5, s));
            try { localStorage.setItem('infobd_font', String(s)); } catch (e) { }
            applyFontSize(s);
        }
        function resetFont() {
            try { localStorage.setItem('infobd_font', '0'); } catch (e) { }
            applyFontSize(0);
        }
        function applyFontSize(s) {
            var html = document.documentElement;
            html.style.fontSize = (100 + s * 8) + '%';
        }
        try { applyFontSize(parseInt(localStorage.getItem('infobd_font') || '0', 10)); } catch (e) { }

        /* Like button */
        document.querySelectorAll('.infobd-like-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.classList.contains('active')) return;
                var id = btn.dataset.id;
                var data = new FormData();
                data.append('action', 'infobd_like');
                data.append('nonce', nonce);
                data.append('id', id);
                fetch(ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (j) {
                        if (j && j.success) {
                            btn.classList.add('active');
                            var c = btn.querySelector('.ilb-count');
                            if (c) c.textContent = j.data.count;
                        }
                    }).catch(function () { });
            });
        });

        /* Bookmark (localStorage) */
        document.querySelectorAll('.infobd-bookmark-btn').forEach(function (btn) {
            var id = btn.dataset.id;
            var key = 'infobd_bm';
            try {
                var list = JSON.parse(localStorage.getItem(key) || '[]');
                if (list.indexOf(id) > -1) btn.classList.add('active');
            } catch (e) { var list = []; }
            btn.addEventListener('click', function () {
                try {
                    var list = JSON.parse(localStorage.getItem(key) || '[]');
                    var idx = list.indexOf(id);
                    if (idx > -1) { list.splice(idx, 1); btn.classList.remove('active'); }
                    else { list.push(id); btn.classList.add('active'); }
                    localStorage.setItem(key, JSON.stringify(list));
                } catch (e) { }
            });
        });

        /* Wishlist button */
        document.querySelectorAll('.infobd-wishlist-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var data = new FormData();
                data.append('action', 'infobd_wishlist');
                data.append('nonce', nonce);
                data.append('id', btn.dataset.id);
                fetch(ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (j) {
                        if (j && j.success) btn.classList.toggle('active');
                    }).catch(function () { });
            });
        });

        /* Copy link buttons */
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var url = btn.getAttribute('data-copy');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function () { flash(btn); });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta); ta.select();
                    try { document.execCommand('copy'); } catch (e) { }
                    document.body.removeChild(ta);
                    flash(btn);
                }
            });
        });
        function flash(btn) {
            var prev = btn.innerText;
            btn.innerText = I18N.copied || 'Copied!';
            setTimeout(function () { btn.innerText = prev; }, 1600);
        }

        /* Reading progress bar */
        var progressBar = document.getElementById('infobd-progress');
        if (progressBar) {
            window.addEventListener('scroll', function () {
                var article = document.querySelector('article.post, .single-post-content');
                if (!article) return;
                var rect = article.getBoundingClientRect();
                var total = rect.height + window.innerHeight * 0.5;
                var scrolled = -rect.top + window.innerHeight * 0.5;
                var pct = Math.max(0, Math.min(100, (scrolled / total) * 100));
                progressBar.style.width = pct + '%';
            });
        }

        /* Image zoom on click (single post images) */
        if (parseInt(O.feat_image_zoom || 0, 10)) {
            document.querySelectorAll('.single-post-content img, article.post img').forEach(function (img) {
                img.style.cursor = 'zoom-in';
                img.addEventListener('click', function () {
                    var ov = document.createElement('div');
                    ov.className = 'infobd-zoom-overlay';
                    var i = document.createElement('img');
                    i.src = img.src;
                    ov.appendChild(i);
                    document.body.appendChild(ov);
                    requestAnimationFrame(function () { ov.classList.add('show'); });
                    ov.addEventListener('click', function () {
                        ov.classList.remove('show');
                        setTimeout(function () { ov.remove(); }, 300);
                    });
                });
            });
        }

        /* Live search suggest */
        if (parseInt(O.feat_search_suggest || 0, 10)) {
            var input = document.querySelector('.header-search-form input[type="search"]');
            if (input) {
                var dropdown;
                var timer;
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    var q = input.value.trim();
                    if (q.length < 3) { if (dropdown) dropdown.remove(); return; }
                    timer = setTimeout(function () {
                        fetch((InfobdMega.home || '/') + '?s=' + encodeURIComponent(q) + '&_ajax=1', { credentials: 'same-origin' })
                            .then(function (r) { return r.text(); })
                            .then(function (html) {
                                if (dropdown) dropdown.remove();
                                dropdown = document.createElement('div');
                                dropdown.className = 'infobd-search-suggest';
                                var temp = document.createElement('div');
                                temp.innerHTML = html;
                                var items = temp.querySelectorAll('.post-card-title a, h2 a');
                                if (!items.length) { dropdown.innerHTML = '<a>No results</a>'; }
                                else {
                                    Array.prototype.slice.call(items, 0, 6).forEach(function (a) {
                                        var n = document.createElement('a');
                                        n.href = a.href; n.textContent = a.textContent;
                                        dropdown.appendChild(n);
                                    });
                                }
                                input.parentNode.appendChild(dropdown);
                            }).catch(function () { });
                    }, 350);
                });
                document.addEventListener('click', function (e) {
                    if (dropdown && !input.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.remove(); dropdown = null;
                    }
                });
            }
        }
    });
})();
