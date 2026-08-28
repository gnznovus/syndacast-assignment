(function () {
    'use strict';

    var config = window.muuControllerAdmin || {};
    var panel = document.getElementById('muu-controller-panel');

    if (!panel || !config.ajaxUrl || !config.nonce) {
        return;
    }

    function setActiveTab(tab) {
        document.querySelectorAll('.muu-controller-tabs [data-muu-tab]').forEach(function (link) {
            link.classList.toggle('nav-tab-active', link.dataset.muuTab === tab);
        });
    }

    function loadPanel(link) {
        var tab = link.dataset.muuTab || 'overview';
        var section = link.dataset.muuSection || '';

        panel.classList.add('is-loading');
        panel.setAttribute('aria-busy', 'true');

        var body = new URLSearchParams();
        body.set('action', 'muu_controller_panel');
        body.set('nonce', config.nonce);
        body.set('tab', tab);
        body.set('section', section);

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString()
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Panel request failed');
                }
                return response.json();
            })
            .then(function (response) {
                if (!response.success || !response.data || typeof response.data.html !== 'string') {
                    throw new Error('Invalid panel response');
                }

                panel.innerHTML = response.data.html;
                setActiveTab(response.data.tab || tab);
                window.history.replaceState({}, '', link.href);
            })
            .catch(function () {
                window.location.assign(link.href);
            })
            .finally(function () {
                panel.classList.remove('is-loading');
                panel.removeAttribute('aria-busy');
            });
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest('[data-muu-tab]');

        if (!link || !link.href || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        loadPanel(link);
    });
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-muu-copy-shortcode]');
        if (!button) {
            return;
        }

        var value = button.getAttribute('data-muu-copy-shortcode') || '';
        if (!value || !navigator.clipboard) {
            return;
        }

        navigator.clipboard.writeText(value).then(function () {
            var original = button.textContent;
            button.textContent = 'Copied';
            window.setTimeout(function () {
                button.textContent = original;
            }, 1200);
        });
    });
})();
