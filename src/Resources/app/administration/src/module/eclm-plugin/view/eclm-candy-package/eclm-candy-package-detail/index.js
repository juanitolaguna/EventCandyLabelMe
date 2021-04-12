import template from './eclm-candy-package-detail.html.twig';
import './eclm-candy-package-detail.scss';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-candy-package-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            joinedCandyPackage: null,
            products: [],
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
        }
    },

    computed: {
        productOptions() {
            return this.products.map((product) => {
                return {
                    value: product.id,
                    label: product.name
                }
            });
        },
        joinedCandyPackageRepository() {
            return this.repositoryFactory.create('eclm_candy_package');
        },
        joinedCandyPackageCriteria() {
            const criteria = new Criteria();
            criteria.getAssociation('candy');
            criteria.getAssociation('package');
            return criteria;
        },
        productRepository() {
            return this.repositoryFactory.create('product');
        },
        productCriteria() {
            const criteria = new Criteria();
            return criteria;
        },
        mediaRepository() {
            return this.repositoryFactory.create('media');
        }
    },

    metaInfo() {
        return {
            title: 'Package Details'
        };
    },

    created() {
        this.componentCreated();
    },

    mounted() {
        this.$refs.eclmSidebar.openContent();
    },

    methods: {
        componentCreated() {
            this.getCandyPackageEntry();

        },

        getCandyPackageEntry() {
            this.joinedCandyPackageRepository
                .get(this.$route.params.id, Shopware.Context.api, this.joinedCandyPackageCriteria)
                .then((entity) => {
                    this.joinedCandyPackage = entity;
                    this.getImage(this.joinedCandyPackage.mediaId);
                    this.getProducts();
                });
        },

        getProducts() {
            if (this.joinedCandyPackage.productId !== null) {
                this.getRelatedProductAndList();
            } else {
                this.getProductList();
            }
        },

        getProductList() {
            this.productRepository
                .search(this.productCriteria, Shopware.Context.api)
                .then((result) => {
                    //clear results
                    this.products = []
                    result.forEach((product) => {
                        this.products.push(product);
                    })
                });
        },

        getRelatedProductAndList() {
            this.productRepository
                .get(this.joinedCandyPackage.productId, Shopware.Context.api)
                .then((result) => {
                    //clear results
                    this.products = []
                    this.products.push(result);

                    this.productRepository
                        .search(this.productCriteria, Shopware.Context.api)
                        .then((result) => {
                            result.forEach((product) => {
                                this.products.push(product);
                            })
                        });
                });
        },

        changeProduct(payload) {
            this.joinedCandyPackage.productId = payload;
        },

        searchProduct(payload) {
            const criteria = new Criteria();
            if (payload !== '') {
                criteria.addFilter(Criteria.contains('name', payload));
            }
            this.productRepository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.products = result;
                    if (!result.length) {
                        const noProducts = {
                            id: '000000',
                            name: 'No results found'
                        }
                        this.products.push(noProducts);
                    }
                });
        },

        getImage(mediaId) {
            if (mediaId) {
                this.mediaRepository
                    .get(mediaId, Shopware.Context.api)
                    .then((entity) => {
                        this.imageUrl = entity.url;
                    });
            }
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        onClickSave() {
            this.isLoading = true;

            this.joinedCandyPackageRepository
                .save(this.joinedCandyPackage, Shopware.Context.api)
                .then(() => {
                    this.getCandyPackageEntry();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to save the Image for the Candy-Package Relation!',
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        },

    }
});
