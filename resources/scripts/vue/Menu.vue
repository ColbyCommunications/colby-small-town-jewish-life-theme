<template>
    <div>
        <nav
            :class="store.menuOpen ? 'navbar-open' : 'navbar-close'"
            class="navbar w-64 fixed overflow-x-scroll top-0 right-0 h-screen z-20 bg-white"
        >
            <div class="flex pr-2 justify-end">
                <button @click="store.closeMenu" class="p-2 text-xl font-bold">
                    <span
                        class="text-black hover:bg-colby-blue hover:rounded hover:text-white material-icons-sharp"
                        >close</span
                    >
                </button>
            </div>
            <ul class="list-none text-center text-black">
                <li v-for="item in colbyBaseThemeMenu" :key="item.ID" class="my-4">
                    <a :href="item.url">{{ item.title }}</a>
                </li>
            </ul>
        </nav>
        <div
            v-if="store.menuOpen"
            class="fixed w-screen h-screen top-0 z-10 bg-neutral-900 opacity-75"
        ></div>
    </div>
</template>
<script setup>
import { useMainStore } from './stores/main';
const store = useMainStore();
const colbyBaseThemeMenu = JSON.parse(window.ColbyBaseTheme.menu);
console.log(colbyBaseThemeMenu);
</script>
