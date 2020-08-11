const {resolve, join} = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');


const dist = resolve(join(__dirname, '../../', 'storefront/dist/storefront/js'))
console.log(dist);

module.exports = ({config}) => {
    console.log(resolve(join(__dirname, '../../', 'node_modules')));
    return {
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
                resolve(join(__dirname, '../../', 'node_modules')),
                resolve(join(__dirname, '../../../../../../../../', 'vendor/shopware/platform/src/Storefront/Resources/app/storefront/node_modules')),
            ]
        },
        resolve: {
            alias: {
                'vue': resolve(join(__dirname, '../../', 'node_modules/vue')),
            },
        }
    };
}
