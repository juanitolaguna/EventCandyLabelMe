const {resolve, join} = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');


// const dist = resolve(join(__dirname, '../../', 'storefront/dist/storefront/js'))
console.log('custom webpack config');

module.exports = ({config}) => {
    console.log(config);
    return {
        mode: 'production',
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    use: [
                        {
                            loader: 'vue-loader'
                        }
                    ]
                }
            ]
        },
        plugins: [
            new VueLoaderPlugin()
        ],
        resolveLoader: {
            modules: [
                resolve(join(__dirname, '../', 'node_modules')),
                resolve(join(__dirname, '../../../../../../../../', 'vendor/shopware/storefront/Resources/app/storefront/node_modules')),
            ]
        },
        resolve: {
            modules: [
                resolve(join(__dirname, '../', 'node_modules')),
            ]
        }
    };
}
