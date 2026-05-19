/**
 * Infobd 3D Theme — main JS
 * Mobile menu, search toggle, tilt, reveal-on-scroll, share/copy, back-to-top, preloader.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ---------- Preloader ---------- */
        var preloader = document.getElementById('infobd-preloader');
        if (preloader) {
            window.addEventListener('load', function () {
                setTimeout(function () {
                    preloader.classList.add('fade-out');
                    setTimeout(function () { preloader.remove(); }, 700);
                }, 300);
            });
            // Safety fallback: remove after 4s no matter what
            setTimeout(function () {
                if (preloader) {
                    preloader.classList.add('fade-out');
                    setTimeout(function () { if (preloader.parentNode) preloader.remove(); }, 700);
                }
            }, 4000);
        }

        /* ---------- Mobile menu toggle ---------- */
        var toggle = document.querySelector('.menu-toggle');
        var menu = document.getElementById('primary-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function () {
                var open = menu.classList.toggle('open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            // Close on outside click
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !toggle.contains(e.target) && menu.classList.contains('open')) {
                    menu.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
            // Submenu touch handling
            var parents = menu.querySelectorAll('li.menu-item-has-children > a, li.page_item_has_children > a');
            parents.forEach(function (a) {
                a.addEventListener('click', function (e) {
                    if (window.innerWidth <= 768) {
                        var li = a.parentElement;
                        if (li.classList.contains('submenu-open')) return;
                        e.preventDefault();
                        li.classList.add('submenu-open');
                        var sub = li.querySelector('.sub-menu, .children');
                        if (sub) {
                            sub.style.opacity = '1';
                            sub.style.visibility = 'visible';
                            sub.style.transform = 'none';
                        }
                    }
                });
            });
        }

        /* ---------- Header search toggle ---------- */
        var searchWrap = document.querySelector('.header-search');
        var searchBtn = document.querySelector('.header-search-toggle');
        if (searchBtn && searchWrap) {
            searchBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                searchWrap.classList.toggle('open');
                var input = searchWrap.querySelector('input[type="search"]');
                if (searchWrap.classList.contains('open') && input) input.focus();
            });
            document.addEventListener('click', function (e) {
                if (!searchWrap.contains(e.target)) searchWrap.classList.remove('open');
            });
        }

        /* ---------- Back to top ---------- */
        var btt = document.getElementById('back-to-top');
        if (btt) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 400) btt.classList.add('visible');
                else btt.classList.remove('visible');
            });
            btt.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ---------- Reveal on scroll ---------- */
        var revealEls = document.querySelectorAll('.post-card, .cat-card, .widget, .hero-main, .hero-side article');
        revealEls.forEach(function (el) { el.classList.add('reveal'); });
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            revealEls.forEach(function (el) { io.observe(el); });
        } else {
            revealEls.forEach(function (el) { el.classList.add('in-view'); });
        }

        /* ---------- 3D tilt on cards ---------- */
        var tiltable = document.querySelectorAll('.post-card, .cat-card, .hero-main, .hero-side article');
        tiltable.forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                if (window.innerWidth < 769) return;
                var rect = card.getBoundingClientRect();
                var x = (e.clientX - rect.left) / rect.width - 0.5;
                var y = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform = 'translateY(-8px) rotateX(' + (-y * 8) + 'deg) rotateY(' + (x * 8) + 'deg)';
            });
            card.addEventListener('mouseleave', function () {
                card.style.transform = '';
            });
        });

        /* ---------- Highlight new posts (within 24h) ---------- */
        document.querySelectorAll('.post-card time, .post-card .post-card-date').forEach(function () {
            // marker-only; we mark via class selector if datetime within 24h
        });

        /* ---------- Copy link buttons ---------- */
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-copy');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function () {
                        var prev = btn.innerText;
                        btn.innerText = (window.Infobd3D && Infobd3D.i18n && Infobd3D.i18n.copied) || 'Copied!';
                        setTimeout(function () { btn.innerText = prev; }, 1800);
                    });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); } catch (e) { }
                    document.body.removeChild(ta);
                    var prev = btn.innerText;
                    btn.innerText = 'Copied!';
                    setTimeout(function () { btn.innerText = prev; }, 1800);
                }
            });
        });

        /* ---------- Smooth scroll for in-page anchors ---------- */
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                var hash = a.getAttribute('href');
                if (hash.length > 1) {
                    var target = document.querySelector(hash);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        /* ---------- Image lazy fade-in ---------- */
        document.querySelectorAll('img').forEach(function (img) {
            if (!img.complete) {
                img.style.opacity = '0';
                img.style.transition = 'opacity .6s ease';
                img.addEventListener('load', function () { img.style.opacity = '1'; });
                img.addEventListener('error', function () { img.style.opacity = '1'; });
            }
        });

    });
})();
