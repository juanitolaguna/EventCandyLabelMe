const { Component } = Shopware;
import template from '../eclm-package-detail/eclm-package-detail.html.twig'

Component.extend('eclm-package-create', 'eclm-package-detail', {
    template,
    methods: {
        getPackage() {
            this.package = this.repository.create(Shopware.Context.api);
            this.package.eventId = this.$route.params.eventId;

        },

        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.package, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'eclm.plugin.packageDetail', params: { id: this.package.id } });
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: 'Failed to create a Package entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        }
    }
});
