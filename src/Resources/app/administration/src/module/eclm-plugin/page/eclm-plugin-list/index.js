import template from './eclm-plugin-list.html.twig';
const { Criteria } = Shopware.Data;

Shopware.Component.register('eclm-plugin-list', {
    template: template,

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
            repository: null,
            candies: null
        };
    },



    created() {
        this.repository = this.repositoryFactory.create('eclm_candy');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.candies = result;
            })
    },

    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: 'Name',
                routerLink: 'eclm.plugin.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'eurProMl',
                dataIndex: 'eurProMl',
                label: '€/100ml',
                inlineEdit: 'number',
                allowResize: true
            }]
        }
    }



});
