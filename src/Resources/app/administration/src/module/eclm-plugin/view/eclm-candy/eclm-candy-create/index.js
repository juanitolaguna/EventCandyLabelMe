const { Component } = Shopware;
import template from '../eclm-candy-detail/eclm-candy-detail.html.twig'

Component.extend('eclm-candy-create', 'eclm-candy-detail', {
    template,
    methods: {
        getCandy() {
            this.candy = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.candy, Shopware.Context.api)
                .then(() => {
                    console.log(this.candy);
                    this.isLoading = false;
                    this.$router.push({ name: 'eclm.plugin.candyDetail', params: { id: this.candy.id } });
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: 'Failed to create a Candy entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        }
    }
});
