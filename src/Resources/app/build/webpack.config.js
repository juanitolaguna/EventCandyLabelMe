    const { resolve, join } = require('path');

    module.exports = ({ config }) => {
        return {
            resolve: {
                alias: {
                    'vuе-loader': resolve(join(__dirname, '../../', 'node_modules/vue-loader')),
                    vue: resolve(join(__dirname, '../../', 'node_modules/vue'))
                    // 'vue-template-compiler': resolve(join(__dirname, '../../', 'node_modules/vue-loader'))
                },
            },
        };
    }
