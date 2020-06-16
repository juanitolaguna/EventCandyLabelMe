import template from './eclm-candy-detail.html.twig';
import './eclm-candy-detail.scss';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;


Component.register('eclm-candy-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            candy: null,
            candySaved: true,
            // displays image in sidebar
            imageUrl: null,

            // strange error workaround, cannot set joinTableEntry without media_id.
            noImage: null,

            //onSave Candy Entity
            isLoading: false,
            processSuccess: false,

            //Package Selection
            packageOptions: [],
            joinedCandyPackages: null,
            //-> packageValues computed (multi-selection)
            //   which are the currently selected packages that belongs
            //   to the current candy entity

            //on Package Select
            filterLoading: true,
        }
    },

    computed: {
        candyRepository() {
            return this.repositoryFactory.create('eclm_candy');
        },
        imageRepository() {
            return this.repositoryFactory.create('media');
        },
        packageRepository() {
            return this.repositoryFactory.create('eclm_package');
        },
        joinedCandyPackageRepository() {
            return this.repositoryFactory.create('eclm_candy_package');
        },
        joinedCandyPackageCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('candyId', this.$route.params.id))
            criteria.addAssociation('package');
            return criteria;
        },
        packageValues() {
            if (!this.joinedCandyPackages) {
                return [];
            }
            return this.joinedCandyPackages.map((cp) => {
                return cp.packageId;
            });
        },
    },

    metaInfo() {
        return {
            title: 'Candy Details'
        };
    },

    created() {
        this.componentCreated().catch((err) => {
            console.error(err);
        });


    },

    mounted() {
        this.$refs.eclmSidebar.openContent();
    },

    methods: {
        async componentCreated() {
            await this.getCandy()
                .then((candy) => {
                    this.candy = candy;
                });
            await this.getImage(this.candy.mediaId);

            // strange error workaround
            this.getNoImage();

            // Create Package Selection
            this.getPackageOptions();
            this.getJoinedCandyPackageValues();

        },


        getCandy() {
            return this.candyRepository
                .get(this.$route.params.id, Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;
            this.candyRepository
                .save(this.candy, Shopware.Context.api)
                .then(() => this.getCandy())
                .then((candy) => {
                    this.candy = candy;
                    this.getImage(candy.mediaId);
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: 'Failed to create a Candy entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        },

        onMediaSelect(payload) {
            this.getImage(payload);
        },

        getImage(mediaId) {
            if (mediaId) {
                this.imageRepository
                    .get(mediaId, Shopware.Context.api)
                    .then((entity) => {
                        this.imageUrl = entity.url;
                    }).catch((err) => {
                    console.error(err);
                })
            }
        },

        saveFinish() {
            this.processSuccess = false;
        },

        getNoImage() {
            let criteria = new Criteria();
            criteria.addFilter(Criteria.equals('fileName', 'noimage'));
            this.imageRepository.search(criteria, Shopware.Context.api).then((image) => {
                this.noImage = image[0];
            }).catch((err) => {
                console.error(err);
            })
        },


        //Package Selection
        getPackageOptions() {
            this.packageRepository
                .search(new Criteria(), Shopware.Context.api)
                .then((packages) => {
                    this.packageOptions = packages;
                })
        },

        getJoinedCandyPackageValues() {
            return this.joinedCandyPackageRepository.search(this.joinedCandyPackageCriteria, Shopware.Context.api)
                .then((result) => {
                    this.joinedCandyPackages = result;
                    this.filterLoading = false;
                })
        },


        onPackageAdd(item) {
            this.filterLoading = true;
            let entity = this.joinedCandyPackageRepository.create(Shopware.Context.api);
            entity.candyId = this.$route.params.id;
            entity.packageId = item.id;
            entity.mediaId = this.noImage.id;

            this.joinedCandyPackageRepository.save(entity, Shopware.Context.api).then((res) => {
                this.getJoinedCandyPackageValues();
            }).catch((err) => {
                console.error(err);
            })
        },

        onPackageRemove(item) {
            const packageToRemove = this.joinedCandyPackages.find((cp) => cp.packageId === item.id);
            this.joinedCandyPackageRepository.delete(packageToRemove.id, Shopware.Context.api)
                .then((res) => {
                    this.getJoinedCandyPackageValues();
                }).catch((err) => {
                console.error(err);
            })

        }


    }
});
