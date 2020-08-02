import template from './eclm-candy-list.html.twig';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-candy-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            candies: null
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
                routerLink: 'eclm.plugin.candyDetail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true,
                required: true
            }, {
                property: 'active',
                dataIndex: 'active',
                label: 'Active',
                allowResize: true,
                inlineEdit: 'boolean',
                align: 'center'
            }]
        }
    },


    created() {
        this.repository = this.repositoryFactory.create('eclm_candy');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.candies = result;
            })
    }
});
