const { Component } = Shopware;
import template from '../eclm-label-detail/eclm-label-detail.html.twig'

Component.extend('eclm-label-create', 'eclm-label-detail', {
    template,
    methods: {
        getLabel() {
            this.label = this.repository.create(Shopware.Context.api);
            this.label.eventId = this.$route.params.eventId;

        },

        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.label, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'eclm.plugin.labelDetail', params: { id: this.label.id } });
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: 'Failed to create a Label entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        }
    }
});
