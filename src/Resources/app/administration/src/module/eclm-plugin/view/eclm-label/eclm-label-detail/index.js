import template from './eclm-label-detail.html.twig';
import './eclm-label-detail.scss';

const {Component, Mixin } = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-label-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            label: null,
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            mediaRepository: null,
        }
    },

    metaInfo() {
        return {
            title: 'Label Details'
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('eclm_label');
        this.getLabel();
    },

    mounted() {
        this.$refs.eclmSidebar.openContent();
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

        getLabel() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.label = entity;
                    this.getImage(this.label.mediaId);
                });
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.label, Shopware.Context.api)
                .then(() => {
                    this.getLabel();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to create a Label entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
