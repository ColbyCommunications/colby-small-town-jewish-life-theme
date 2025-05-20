const parentConfig = require('../colby-college-theme/tailwind.config');

const childConfig = {
    content: ['./src/**/*.{twig,vue}', '../colby-college-theme/src/**/*.{twig,vue}'],
    theme: {
        ...parentConfig.theme,
        backgroundImage: {
            hero__pattern: 'url("~/src/images/hero__pattern.jpg")',
            notfound__pattern: 'url("~/src/images/miller3.jpeg")',
            darkinterstitial__pattern: 'url("~/src/images/Background-3.jpg")',
            mediacontextsection___pattern: 'url("~/src/images/media-context-section_bg.jpg")',
            marble__pattern: 'url("~/src/images/mountain.jpg")',
            bluemarble__pattern: 'url("~/src/images/Treatment-36.jpg")',
            blueslate__pattern: 'url("~/src/images/Treatment-14.jpg")',
        },
    },
    plugins: [...parentConfig.plugins],
};

module.exports = childConfig;
