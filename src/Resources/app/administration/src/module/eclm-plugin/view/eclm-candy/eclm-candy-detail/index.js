import template from './eclm-candy-detail.html.twig';
import './eclm-candy-detail.scss';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('eclm-candy-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            candy: null,
            imageUrl: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            mediaRepository: null,
        }
    },

    metaInfo() {
        return {
            title: 'Candy Details'
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('eclm_candy');
        this.getCandy();
    },

    mounted() {
        this.$refs.eclmSidebar.openContent();
    },

    methods: {

        getImage(mediaId) {
            this.mediaRepository = this.repositoryFactory.create('media');
            let criteria = new Criteria();
            criteria.setIds([mediaId]);

            this.mediaRepository
                .search(criteria, Shopware.Context.api)
                .then((entity) => {
                this.imageUrl = entity[0].url;
            });

        },

        getCandy() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.candy = entity;
                    this.getImage(this.candy.mediaId);
                });
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.candy, Shopware.Context.api)
                .then(() => {
                    this.getCandy();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to save the Candy Entity',
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
