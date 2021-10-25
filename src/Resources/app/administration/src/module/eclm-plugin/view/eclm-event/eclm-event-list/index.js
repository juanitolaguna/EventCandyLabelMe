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
            }, {
                property: 'notAvailable',
                dataIndex: 'notAvailable',
                label: 'Not Available',
                allowResize: true,
                inlineEdit: 'boolean',
                align: 'center'
            }, {
                property: 'position',
                dataIndex: 'position',
                label: 'Position',
                allowResize: true,
                inlineEdit: 'number',
                align: 'center'
            }
            ]
        },
        repository() {
            return this.repositoryFactory.create('eclm_event');
        }

    },


    created() {
        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.events = result;
            })
    }
});
