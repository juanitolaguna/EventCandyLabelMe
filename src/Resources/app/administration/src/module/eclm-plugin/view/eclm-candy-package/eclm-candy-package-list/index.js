import template from './eclm-candy-package-list.html.twig';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;


Component.register('eclm-candy-package-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            joinedCandyPackages: null,
        }
    },

    computed: {
        joinedCandyPackageRepository() {
            return this.repositoryFactory.create('eclm_candy_package');
        },
        joinedCandyPackageCriteria() {
            const criteria = new Criteria();
            criteria.getAssociation('candy');
            criteria.getAssociation('package');
            criteria.getAssociation('media');
            return criteria;
        },


        columns() {
            return [
                {
                    property: 'id',
                    label: 'Id',
                    routerLink: 'eclm.plugin.candyPackageDetail',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'candy.name',
                    label: 'Candy Name',
                    allowResize: true,
                    // primary: true,

                }, {
                    property: 'package.name',
                    label: 'Package Name',
                    allowResize: true
                }, {
                    property: 'media.fileName',
                    label: 'Image',
                    allowResize: true
                }]
        }
    },

    methods: {
        componentCreated() {
            this.getJoinedCandyPackages();
        },
        getJoinedCandyPackages() {
            this.joinedCandyPackageRepository
                .search(this.joinedCandyPackageCriteria, Shopware.Context.api)
                .then((result) => {
                    this.joinedCandyPackages = result;
                })
        }

    },

    created() {
        this.componentCreated();
    }


});
