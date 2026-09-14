/* LinkEasy Social — landing interactions (vanilla JS, no dependencies) */
(function () {
    'use strict';

    var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Lightweight, viewport-scoped motion ---------- */
    var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    var savedMotion = '';
    try { savedMotion = localStorage.getItem('les-motion') || ''; } catch (_) {}
    var motionOff = savedMotion === 'off';
    var motionButtons = document.querySelectorAll('[data-motion-toggle]');
    var motionSections = document.querySelectorAll('.motion-section');
    motionSections.forEach(function (section) {
        var glyphs = section.querySelectorAll('.command-card-heading .icon, .flow-icon .icon, .flow-center-logo, .studio-kicker .icon, .marquee-item .icon, .module-icon .icon, .mock-frame-title .icon, .icon-tile .icon, .platform-chip .icon, .integration-icon .icon, .step-icon .icon, .final-logo, .platform-avatar .icon');
        Array.from(glyphs).slice(0, 3).forEach(function (glyph, i) {
            glyph.classList.add('motion-glyph');
            glyph.style.setProperty('--motion-delay', (i * .45) + 's');
        });
    });
    if ('IntersectionObserver' in window) {
        var motionObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                entry.target.classList.toggle('in-view', entry.isIntersecting);
                if (entry.isIntersecting && entry.target.querySelector('[data-timeline]')) {
                    window.dispatchEvent(new Event('les:timeline-visible'));
                }
            });
        }, { threshold: 0, rootMargin: '0px' });
        motionSections.forEach(function (section) { motionObserver.observe(section); });
    } else { motionSections.forEach(function (section) { section.classList.add('in-view'); }); }
    function applyMotion() {
        var disabled = motionOff || motionQuery.matches;
        document.body.classList.toggle('motion-enabled', !disabled);
        document.body.classList.toggle('motion-paused', disabled);
        motionButtons.forEach(function (button) {
            button.hidden = false;
            button.disabled = motionQuery.matches;
            button.setAttribute('aria-pressed', String(!disabled));
            button.setAttribute('aria-label', motionQuery.matches ? 'Animations disabled by your reduced-motion preference' : (disabled ? 'Enable website animations' : 'Pause website animations'));
            button.querySelector('span').textContent = motionQuery.matches ? 'Reduced motion' : (disabled ? 'Enable motion' : 'Pause motion');
            button.querySelector('use').setAttribute('href', disabled ? '#i-play' : '#i-pause');
        });
    }
    motionButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            motionOff = !motionOff;
            try { localStorage.setItem('les-motion', motionOff ? 'off' : 'on'); } catch (_) {}
            applyMotion();
        });
    });
    if (motionQuery.addEventListener) motionQuery.addEventListener('change', applyMotion);
    function tabVisibility() { document.body.classList.toggle('motion-tab-hidden', document.hidden); }
    document.addEventListener('visibilitychange', tabVisibility);
    tabVisibility(); applyMotion();

    /* ---------- Illustrative analytics, with keyboard-accessible metric tabs ---------- */
    var metrics = Array.from(document.querySelectorAll('[data-command-metric]'));
    var chartPaths = {
        reach: ['Total reach', 'M0 115C35 115 56 86 84 90S142 116 175 78S224 91 265 51S321 76 354 45S417 70 447 31S505 42 540 12'],
        engagement: ['Engagement', 'M0 105C36 105 51 67 84 72S141 101 175 55S224 39 265 58S320 23 354 35S418 57 447 28S504 20 540 8'],
        audience: ['Audience', 'M0 124C35 121 56 102 84 100S142 88 175 91S224 72 265 64S321 70 354 42S417 37 447 29S505 17 540 9']
    };
    function selectMetric(tab) {
        var data = chartPaths[tab.getAttribute('data-command-metric')];
        var chart = document.querySelector('.command-chart');
        metrics.forEach(function (candidate) {
            var active = candidate === tab;
            candidate.classList.toggle('is-active', active);
            candidate.setAttribute('aria-selected', String(active));
            candidate.tabIndex = active ? 0 : -1;
        });
        document.getElementById('command-chart-panel').setAttribute('aria-labelledby', tab.id);
        document.querySelector('[data-command-series]').textContent = data[0];
        chart.setAttribute('aria-label', 'Illustrative ' + data[0].toLowerCase() + ' trend over seven days, not live account data');
        chart.querySelector('.command-chart-line').setAttribute('d', data[1]);
        chart.querySelector('.command-chart-area').setAttribute('d', data[1] + 'V142H0Z');
    }
    metrics.forEach(function (tab, i) {
        tab.addEventListener('click', function () { selectMetric(tab); });
        tab.addEventListener('keydown', function (event) {
            var next;
            if (event.key === 'ArrowRight') next = (i + 1) % metrics.length;
            else if (event.key === 'ArrowLeft') next = (i + metrics.length - 1) % metrics.length;
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = metrics.length - 1;
            else return;
            event.preventDefault(); selectMetric(metrics[next]); metrics[next].focus();
        });
    });

    /* ---------- Sticky header ---------- */
    var header = document.getElementById('siteHeader');
    function onScroll() {
        if (!header) return;
        header.classList.toggle('is-scrolled', window.scrollY > 64);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---------- Mobile navigation ---------- */
    var navToggle = document.querySelector('.nav-toggle');
    var mobileNav = document.getElementById('mobileNav');
    var navBackdrop = document.getElementById('navBackdrop');
    function setMobileNav(open) {
        if (!mobileNav || !navToggle) return;
        mobileNav.hidden = !open;
        if (navBackdrop) {
            navBackdrop.hidden = !open;
            navBackdrop.classList.toggle('is-open', open);
        }
        document.querySelector('.site-header').classList.toggle('menu-open', open);
        navToggle.setAttribute('aria-expanded', String(open));
        navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        navToggle.querySelector('use').setAttribute('href', open ? '#i-close' : '#i-menu');
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) { var first = mobileNav.querySelector('a'); if (first) first.focus(); }
        else if (mobileNav.contains(document.activeElement)) { navToggle.focus(); }
    }
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            setMobileNav(mobileNav.hidden);
        });
        mobileNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setMobileNav(false); });
        });
        if (navBackdrop) {
            navBackdrop.addEventListener('click', function () { setMobileNav(false); });
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !mobileNav.hidden) { setMobileNav(false); navToggle.focus(); }
            if (e.key === 'Tab' && !mobileNav.hidden) {
                var controls = [navToggle].concat(Array.from(mobileNav.querySelectorAll('a, button')));
                var first = controls[0], last = controls[controls.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1120 && mobileNav && !mobileNav.hidden) setMobileNav(false);
    });

    /* ---------- Scroll reveal + line drawing + timeline ---------- */
    var revealEls = document.querySelectorAll('.reveal');
    var drawEls = document.querySelectorAll('[data-draw-lines]');

    function markVisible(el) { el.classList.add('is-visible'); }

    if ('IntersectionObserver' in window && !prefersReduced) {
        var revealObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var delay = el.getAttribute('data-reveal-delay');
                    if (delay) el.style.setProperty('--rd', delay + 'ms');
                    markVisible(el);
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { revealObserver.observe(el); });

        var drawObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-drawn');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.35 });
        drawEls.forEach(function (el) { drawObserver.observe(el); });
    } else {
        revealEls.forEach(markVisible);
        drawEls.forEach(function (el) { el.classList.add('is-drawn'); });
    }

    /* ---------- Animated counters ---------- */
    function animateCounter(el) {
        var target = parseFloat(el.getAttribute('data-count')) || 0;
        var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        var prefix = el.getAttribute('data-prefix') || '';
        var suffix = el.getAttribute('data-suffix') || '';
        if (prefersReduced) {
            el.textContent = prefix + target.toFixed(decimals) + suffix;
            return;
        }
        var duration = 1400;
        var start = null;
        function frame(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }
    var counters = document.querySelectorAll('[data-count]');
    if ('IntersectionObserver' in window) {
        var counterObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.6 });
        counters.forEach(function (el) { counterObserver.observe(el); });
    } else {
        counters.forEach(animateCounter);
    }

    /* ---------- How-it-works timeline progress ---------- */
    var timeline = document.querySelector('[data-timeline]');
    if (timeline) {
        var progress = timeline.querySelector('.steps-progress');
        var steps = Array.prototype.slice.call(timeline.querySelectorAll('.step-item'));
        function updateTimeline() {
            var owner = timeline.closest('.motion-section');
            if (owner && !owner.classList.contains('in-view')) return;
            var line = timeline.querySelector('.steps-track');
            var baseRect = timeline.getBoundingClientRect();
            var firstIcon = steps[0].querySelector('.step-icon').getBoundingClientRect();
            var lastIcon = steps[steps.length - 1].querySelector('.step-icon').getBoundingClientRect();
            line.style.top = (firstIcon.top + firstIcon.height / 2 - baseRect.top) + 'px';
            line.style.height = (lastIcon.top + lastIcon.height / 2 - firstIcon.top - firstIcon.height / 2) + 'px';
            line.style.bottom = 'auto';
            var rect = line.getBoundingClientRect();
            var trigger = window.innerHeight * 0.62;
            var activeIndex = -1;
            steps.forEach(function (step, i) {
                var marker = step.querySelector('.step-marker').getBoundingClientRect();
                if (marker.top <= trigger) { activeIndex = i; step.classList.add('is-active'); }
                else { step.classList.remove('is-active'); }
            });
            if (activeIndex >= 0) {
                var markerRect = steps[activeIndex].querySelector('.step-marker').getBoundingClientRect();
                var fraction = Math.min(1, ((markerRect.top + markerRect.height / 2) - rect.top) / rect.height);
                progress.style.transform = 'scaleY(' + fraction + ')';
            } else {
                progress.style.transform = 'scaleY(0)';
            }
        }
        window.addEventListener('scroll', updateTimeline, { passive: true });
        window.addEventListener('resize', updateTimeline);
        window.addEventListener('les:timeline-visible', updateTimeline);
        updateTimeline();
    }

    /* ---------- Accordion (FAQ) ---------- */
    document.querySelectorAll('[data-accordion]').forEach(function (accordion) {
        accordion.querySelectorAll('.faq-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var item = trigger.closest('.faq-item');
                var panel = document.getElementById(trigger.getAttribute('aria-controls'));
                var isOpen = item.classList.contains('is-open');
                var iconUse = trigger.querySelector('use');

                accordion.querySelectorAll('.faq-item.is-open').forEach(function (other) {
                    if (other !== item) {
                        other.classList.remove('is-open');
                        var otherTrigger = other.querySelector('.faq-trigger');
                        var otherPanel = document.getElementById(otherTrigger.getAttribute('aria-controls'));
                        otherPanel.hidden = true;
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        otherTrigger.querySelector('use').setAttribute('href', '#i-plus');
                    }
                });

                item.classList.toggle('is-open', !isOpen);
                panel.hidden = isOpen;
                trigger.setAttribute('aria-expanded', String(!isOpen));
                if (iconUse) iconUse.setAttribute('href', isOpen ? '#i-plus' : '#i-minus');
            });
        });
    });

    /* ---------- Tabs (use cases) ---------- */
    document.querySelectorAll('[data-tabs]').forEach(function (tabs) {
        var tabButtons = tabs.querySelectorAll('[data-tab]');
        var panels = tabs.querySelectorAll('[data-panel]');
        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-tab');
                tabButtons.forEach(function (b) {
                    var active = b === button;
                    b.classList.toggle('is-active', active);
                    b.setAttribute('aria-selected', String(active));
                });
                panels.forEach(function (panel) {
                    var active = panel.getAttribute('data-panel') === target;
                    panel.classList.toggle('is-active', active);
                    panel.hidden = !active;
                });
            });
        });
    });

    /* ---------- Pricing billing toggle ---------- */
    var billingToggle = document.querySelector('[data-billing-toggle]');
    if (billingToggle) {
        billingToggle.querySelectorAll('button').forEach(function (button) {
            button.addEventListener('click', function () {
                var cycle = button.getAttribute('data-billing');
                billingToggle.querySelectorAll('button').forEach(function (b) {
                    b.classList.toggle('is-active', b === button);
                    b.setAttribute('aria-pressed', String(b === button));
                });
                document.querySelectorAll('.price-value[data-price-monthly]').forEach(function (value) {
                    value.textContent = cycle === 'yearly'
                        ? value.getAttribute('data-price-yearly')
                        : value.getAttribute('data-price-monthly');
                });
                document.querySelectorAll('[data-cadence-monthly]').forEach(function (cadence) {
                    cadence.textContent = cadence.getAttribute(
                        cycle === 'yearly' ? 'data-cadence-yearly' : 'data-cadence-monthly');
                });
                document.querySelectorAll('[data-yearly-note]').forEach(function (note) {
                    note.hidden = cycle !== 'yearly';
                });
                document.querySelectorAll('.js-managed-cta').forEach(function (cta) {
                    cta.setAttribute('href', cta.getAttribute(
                        cycle === 'yearly' ? 'data-href-yearly' : 'data-href-monthly'));
                });
            });
        });
    }

    /* ---------- Scroll spy for primary nav ---------- */
    var navLinks = document.querySelectorAll('.main-nav a[data-nav-id]');
    if (navLinks.length && 'IntersectionObserver' in window) {
        var spyMap = {};
        navLinks.forEach(function (link) { spyMap[link.getAttribute('data-nav-id')] = link; });
        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                var link = spyMap[entry.target.id];
                if (link && entry.isIntersecting) {
                    navLinks.forEach(function (l) { l.classList.remove('is-active'); });
                    link.classList.add('is-active');
                }
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        ['features', 'how-it-works', 'integrations', 'pricing'].forEach(function (id) {
            var section = document.getElementById(id);
            if (section) spy.observe(section);
        });
    }

    /* ---------- Quote details: same subject rules as PHP, no API secrets ---------- */
    var contactForm = document.querySelector('[data-contact-form]');
    if (contactForm) {
        var contactSubject = contactForm.querySelector('[name="subject"]');
        var quoteDetails = contactForm.querySelector('[data-quote-details]');
        var quoteSubjects = JSON.parse(contactForm.getAttribute('data-quote-subjects'));
        function updateContact() {
            var quote = quoteSubjects.indexOf(contactSubject.value) !== -1;
            quoteDetails.hidden = !quote;
            quoteDetails.removeAttribute('data-initial-hidden');
            quoteDetails.disabled = !quote;
            quoteDetails.querySelectorAll('[data-quote-required]').forEach(function (field) { field.required = quote; });
            contactForm.querySelector('[data-message-label]').textContent = quote ? 'Goals & requirements *' : 'Your message *';
            contactForm.querySelector('[data-contact-submit]').textContent = quote ? 'Request my setup quote' : 'Send message';
            contactForm.querySelector('[data-contact-note]').textContent = quote ? 'No payment is taken. Scope and pricing are agreed with you first.' : 'We’ll reply to the email address you provide.';
        }
        contactSubject.addEventListener('change', updateContact);
        window.addEventListener('pageshow', updateContact);
        updateContact();
        document.querySelectorAll('[data-choose-quote]').forEach(function (link) {
            link.addEventListener('click', function () { contactSubject.value = quoteSubjects[0]; updateContact(); contactSubject.focus({preventScroll: true}); });
        });
        var errorSummary = document.querySelector('[data-error-summary]');
        if (errorSummary) errorSummary.focus();
    }

    /* ---------- Flash dismiss ---------- */
    document.querySelectorAll('.flash-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.closest('.flash')?.remove();
        });
    });

    /* ---------- Password visibility toggles ---------- */
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-target'));
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.querySelector('use').setAttribute('href', show ? '#i-eye-off' : '#i-eye');
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    /* ---------- Session expiry watcher (only on authed pages) ---------- */
    if (document.querySelector('[data-session-watch]')) {
        var sessionModal = document.getElementById('sessionModal');
        var warned = false;
        setInterval(function () {
            var base = document.body.getAttribute('data-base-path') || '';
            fetch(base + '/auth/session-check', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.authenticated && !warned && sessionModal) {
                        warned = true;
                        sessionModal.hidden = false;
                    }
                })
                .catch(function () { /* network hiccup: stay silent */ });
        }, 60000);
    }
})();
