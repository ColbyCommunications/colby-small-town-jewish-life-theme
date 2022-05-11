import { defineStore } from 'pinia';

export const useMainStore = defineStore('main', {
    state: () => {
        return {
            menuOpen: false,
        };
    },
    actions: {
        openMenu() {
            const el = document.body;
            el.classList.add('no-scroll');
            this.menuOpen = true;
        },
        closeMenu() {
            const el = document.body;
            el.classList.remove('no-scroll');
            this.menuOpen = false;
        },
    },
});
