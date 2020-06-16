const { Component } = Shopware;
import template from '../eclm-candy-detail/eclm-candy-detail.html.twig'

Component.extend('eclm-candy-create', 'eclm-candy-detail', {
    template,

    data() {
        return {
            candySaved: false
        }
    },

    methods: {
        async componentCreated() {
            this.getCandy();
        },

        getCandy() {
            this.candy = this.candyRepository.create(Shopware.Context.api);
        },


        onClickSave() {
            this.isLoading = true;

            this.candyRepository
                .save(this.candy, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'eclm.plugin.candyDetail', params: { id: this.candy.id } });
                }).catch((exception) => {
                this.isLoading = false;
                this.candySaved = true;

                this.createNotificationError({
                    title: 'Failed to create a Candy entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        }
    }
});
