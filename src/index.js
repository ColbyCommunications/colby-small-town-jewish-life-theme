import { createApp } from 'vue/dist/vue.esm-bundler';
import mitt from 'mitt';
import { $vfm, VueFinalModal, ModalsContainer } from 'vue-final-modal';
import InstantSearch from 'vue-instantsearch/vue3/es';

import './styles/styles.scss';

const embeds = document.getElementsByClassName('embed');
const mains = [];

// Array that sweeps the document for all embed components and removes them
Array.from(embeds).forEach((e) => {
    const embedMain = e.getElementsByClassName('embed__main')[0];
    mains.push(embedMain);
    e.removeChild(embedMain);
});

const emitter = mitt();

import baseComponents from '../../colby-college-theme/src/components.js';

import childComponents from './components.js';

const app = createApp({
    components: Object.assign(baseComponents, childComponents),
});

app.config.globalProperties.emitter = emitter;
app.use(InstantSearch);

app.mount('#app');

// Replace all embeds following Vue mounting
for (let i = 0; i < embeds.length; i += 1) {
    embeds[i].append(mains[i]);
}

export default app;
