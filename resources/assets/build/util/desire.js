/* eslint-disable import/no-dynamic-require */
/* eslint-disable global-require */

/**
 * @export
 * @param {string} dependency
 * @param {any} [fallback]
 * @return {any}
 */
module.exports = (dependency, fallback) => {
    try {
        require.resolve(dependency);
    } catch (err) {
        return fallback;
    }
    return require(dependency);
};
