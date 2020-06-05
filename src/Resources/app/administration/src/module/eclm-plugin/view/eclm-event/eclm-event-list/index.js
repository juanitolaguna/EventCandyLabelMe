import template from './eclm-event-list.html.twig';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-event-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            events: null
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
                routerLink: 'eclm.plugin.eventDetail',
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
        this.repository = this.repositoryFactory.create('eclm_event');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.events = result;
            })
    }
});
