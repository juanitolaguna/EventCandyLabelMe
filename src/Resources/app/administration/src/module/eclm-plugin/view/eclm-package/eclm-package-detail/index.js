import template from './eclm-package-detail.html.twig';
import './eclm-package-detail.scss';

const {Component, Mixin } = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-package-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            package: null,
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            mediaRepository: null,
        }
    },

    metaInfo() {
        return {
            title: 'Package Details'
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('eclm_package');
        this.getPackage();
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

        getPackage() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.package = entity;
                    this.getImage(this.package.mediaId);
                });
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.package, Shopware.Context.api)
                .then(() => {
                    this.getPackage();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to create a Package entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
