import template from './eclm-event-detail.html.twig';
import './eclm-event-detail.scss';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-event-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            event: null,
            labels: null,
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            mediaRepository: null,
            labelRepository: null,
            eventSaved: true
        }
    },

    computed: {
        labelColumns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: 'Name',
                routerLink: 'eclm.plugin.labelDetail',
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
                property: 'position',
                dataIndex: 'position',
                label: 'Position',
                allowResize: true,
                inlineEdit: 'number',
                align: 'center'
            }]
        }
    },

    metaInfo() {
        return {
            title: 'Event Details'
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('eclm_event');
        this.getEvent();
    },

    mounted() {
        this.$refs.eclmEventSidebar.openContent();
    },

    methods: {

        getImage(mediaId) {
            if (mediaId) {
                this.mediaRepository = this.repositoryFactory.create('media');

                this.mediaRepository
                    .get(mediaId, Shopware.Context.api)
                    .then((entity) => {
                        this.imageUrl = entity.url;
                    });
            }
        },

         async getEvent() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.event = entity;
                    this.getImage(this.event.mediaId);

                    this.labelRepository = this.repositoryFactory.create(
                        this.event.labels.entity,
                        this.event.labels.source
                    );
                    this.getLabel();
                });
        },

        getLabel() {
            this.labelRepository.search(new Criteria(), Shopware.Context.api)
                .then((labels) => {
                    this.labels = labels;
                }).catch((error) => {
                this.createNotificationError({
                    title: 'Failed to get the Labels for this event type!',
                    message: error
                });
            })
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.event, Shopware.Context.api)
                .then(() => {
                    this.getEvent();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to create an Event! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
