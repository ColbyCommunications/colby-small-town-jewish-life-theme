// import external dependencies
import 'jquery';

// import '../styles/main.scss';
// Import everything from autoload
// eslint-disable-next-line import/no-unresolved
import './autoload/**/*';

// import local dependencies
import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import aboutUs from './routes/about';

if (module.hot) {
    module.hot.accept();
}

/** Populate Router instance with DOM routes */
const routes = new Router({
    // All pages
    common,
    // Home page
    home,
    // About Us page, note the change from about-us to aboutUs.
    aboutUs,
});

// Load Events
jQuery(document).ready(() => routes.loadEvents());
