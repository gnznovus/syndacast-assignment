'use strict';

const menuToggle = document.querySelector('.site-header__menu-toggle');
const menuPanel = document.getElementById('primary-menu-panel');

if (menuToggle && menuPanel) {
    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        menuPanel.hidden = isOpen;
    });
}
