import template from './eclm-package-list.html.twig';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-package-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            packages: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },


    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: 'Name',
                routerLink: 'eclm.plugin.packageDetail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true,
                required: true
            }, {
                property: 'milliliters',
                dataIndex: 'milliliters',
                label: 'Milliliter',
                inlineEdit: 'number',
                allowResize: true
            },{
                property: 'active',
                dataIndex: 'active',
                label: 'Active',
                allowResize: true,
                inlineEdit: 'boolean',
                align: 'center'
            }
            ]
        }
    },


    created() {
        this.repository = this.repositoryFactory.create('eclm_package');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.packages = result;
            })
    }
});
