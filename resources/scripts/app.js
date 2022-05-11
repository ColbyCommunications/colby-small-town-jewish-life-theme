import { domReady } from '@roots/sage/client';

import { createPinia } from 'pinia';

import { createApp } from 'vue';

import HeaderMenuButton from './vue/HeaderMenuButton.vue';
import Menu from './vue/Menu.vue';

/**
 * app.main
 */
const main = async (err) => {
    if (err) {
        // handle hmr errors
        console.error(err);
    }
    const store = createPinia();
    // application code
    const headerMenuButton = createApp(HeaderMenuButton);
    headerMenuButton.use(store);
    headerMenuButton.mount('#vue-menu-button');

    const menu = createApp(Menu);
    menu.use(store);
    menu.mount('#vue-menu');
};

/**
 * Initialize
 *
 * @see https://webpack.js.org/api/hot-module-replacement
 */
domReady(main);
import.meta.webpackHot?.accept(main);
