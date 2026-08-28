(function () {
    'use strict';

    function getVisual(component, host) {
        const hostBounds = host.getBoundingClientRect();
        const images = Array.from(host.querySelectorAll('img')).filter(function (image) {
            return !component.contains(image) && image.getBoundingClientRect().width > 0;
        });
        const image = images.sort(function (a, b) {
            const aBounds = a.getBoundingClientRect();
            const bBounds = b.getBoundingClientRect();
            return (bBounds.width * bBounds.height) - (aBounds.width * aBounds.height);
        })[0];

        if (!image) {
            return { top: 0, left: 0, width: hostBounds.width, height: hostBounds.height };
        }

        const imageBounds = image.getBoundingClientRect();
        return {
            top: imageBounds.top - hostBounds.top,
            left: imageBounds.left - hostBounds.left,
            width: imageBounds.width,
            height: imageBounds.height
        };
    }

    function applyVisualBounds(component, host) {
        const visual = getVisual(component, host);
        component.style.setProperty('--muu-ec-visual-top', visual.top + 'px');
        component.style.setProperty('--muu-ec-visual-left', visual.left + 'px');
        component.style.setProperty('--muu-ec-visual-width', visual.width + 'px');
        component.style.setProperty('--muu-ec-visual-height', visual.height + 'px');
    }

    function mountComponent(component) {
        if (!component || component.dataset.muuMounted === 'true') return;

        const widget = component.closest('.elementor-widget-shortcode, .elementor-widget');
        const selector = component.dataset.targetSelector || '.muu-hero-host';
        let host = null;

        try {
            host = component.closest(selector) || document.querySelector(selector);
        } catch (error) {
            component.dataset.muuError = 'invalid-selector';
            return;
        }

        if (!host && widget) {
            host = widget.closest('.e-con, .elementor-section, .elementor-container');
        }

        if (widget) widget.classList.add('muu-ec-host-widget');
        if (host) {
            host.classList.add('muu-ec-host-container');
            if (component.parentElement !== host) host.prepend(component);
            applyVisualBounds(component, host);
            if (component.classList.contains('muu-ec-orange-shape')) {
                component.classList.toggle('is-shape-visible', Boolean(host.querySelector(':scope > .muu-ec-nav-lefttab.is-menu-open')));
            }
        }
        component.dataset.muuMounted = 'true';
    }

    function syncComponent(component) {
        const host = component.parentElement;
        if (!host || !host.classList.contains('muu-ec-host-container')) return;
        applyVisualBounds(component, host);
    }

    function mountAll(root) {
        const selector = '.muu-ec-nav-lefttab, .muu-ec-orange-shape';
        if (root.matches && root.matches(selector)) mountComponent(root);
        if (root.querySelectorAll) root.querySelectorAll(selector).forEach(mountComponent);
    }

    mountAll(document);
    document.addEventListener('DOMContentLoaded', function () { mountAll(document); });
    document.addEventListener('load', function (event) {
        if (!event.target.matches || !event.target.matches('img')) return;
        document.querySelectorAll('.muu-ec-nav-lefttab, .muu-ec-orange-shape').forEach(syncComponent);
    }, true);

    if ('ResizeObserver' in window) {
        const resizeObserver = new ResizeObserver(function (entries) {
            entries.forEach(function (entry) {
                entry.target.querySelectorAll(':scope > .muu-ec-nav-lefttab, :scope > .muu-ec-orange-shape').forEach(syncComponent);
            });
        });

        document.querySelectorAll('.muu-ec-host-container').forEach(function (host) {
            resizeObserver.observe(host);
        });

        const hostObserver = new MutationObserver(function () {
            document.querySelectorAll('.muu-ec-host-container:not([data-muu-resize-watched])').forEach(function (host) {
                host.dataset.muuResizeWatched = 'true';
                resizeObserver.observe(host);
            });
        });
        hostObserver.observe(document.documentElement, { childList: true, subtree: true });
    }

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) mountAll(node);
            });
        });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });

    function animatePanel(component, panel, willOpen) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const startWidth = panel.getBoundingClientRect().width;

        panel.style.transition = 'none';
        component.classList.toggle('is-menu-open', willOpen);
        const endWidth = panel.getBoundingClientRect().width;
        panel.style.removeProperty('transition');

        panel.getAnimations().forEach(function (animation) { animation.cancel(); });
        if (reduceMotion || Math.abs(endWidth - startWidth) < 1) return;

        panel.style.overflow = 'hidden';
        const animation = panel.animate(
            [{ width: startWidth + 'px' }, { width: endWidth + 'px' }],
            { duration: 750, easing: 'cubic-bezier(.22, 1, .36, 1)' }
        );
        animation.finished.then(function () {
            panel.style.removeProperty('overflow');
        }).catch(function () {});
    }

    function setMenuState(component, willOpen) {
        const button = component.querySelector('.muu-ec-menu-toggle');
        const panel = component.querySelector('.muu-ec-lefttab');
        button.setAttribute('aria-expanded', String(willOpen));
        button.setAttribute('aria-label', willOpen ? 'Close menu' : 'Open menu');
        panel.setAttribute('aria-hidden', String(!willOpen));
        panel.inert = !willOpen;
        animatePanel(component, panel, willOpen);
        component.parentElement.querySelectorAll(':scope > .muu-ec-orange-shape').forEach(function (shape) {
            shape.classList.toggle('is-shape-visible', willOpen);
        });
        document.dispatchEvent(new CustomEvent('muu:menu-toggle', { detail: { open: willOpen, component: component } }));
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.muu-ec-menu-toggle, .muu-ec-panel-close');
        if (!button) return;

        const component = button.closest('.muu-ec-nav-lefttab');
        const isClose = button.classList.contains('muu-ec-panel-close');
        setMenuState(component, isClose ? false : !component.classList.contains('is-menu-open'));
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.muu-ec-nav-lefttab.is-menu-open').forEach(function (component) {
            setMenuState(component, false);
            component.querySelector('.muu-ec-menu-toggle').focus();
        });
    });
})();
