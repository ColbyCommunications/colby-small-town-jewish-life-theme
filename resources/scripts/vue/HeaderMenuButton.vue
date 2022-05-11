<template>
    <div>
        <div class="space-x-1 js-focus-visible">
            <div class="inline-block cursor-pointer open-menu-wrapper">
                <button
                    class="open-menu bg-white p-2 rounded-lg group transition-colors selected:bg-black selected:bg-opacity-[7%] disabled:pointer-events-none"
                    aria-label="Open menu (modal)"
                    @click="store.openMenu"
                >
                    <svg
                        class="w-6 h-6 group-hover:scale-125 transform transition-transform"
                        aria-hidden="true"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M 0 11 L 0 13 L 24 13 L 24 11 L 0 11 Z"></path>
                        <path d="M 0 18 L 0 20 L 24 20 L 24 18 L 0 18 Z"></path>
                        <path d="M 0 4 L 0 6 L 24 6 L 24 4 L 0 4 Z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useMainStore } from './stores/main';

const store = useMainStore();
</script>
