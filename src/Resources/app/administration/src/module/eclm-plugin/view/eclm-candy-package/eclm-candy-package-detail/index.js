import template from './eclm-candy-package-detail.html.twig';
import './eclm-candy-package-detail.scss';

const {Component, Mixin } = Shopware;
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
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
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
        }
    }
});
