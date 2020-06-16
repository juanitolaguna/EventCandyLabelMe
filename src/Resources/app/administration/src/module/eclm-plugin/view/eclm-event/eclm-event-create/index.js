const { Component } = Shopware;
import template from '../eclm-event-detail/eclm-event-detail.html.twig'

Component.extend('eclm-event-create', 'eclm-event-detail', {
    template,

    data() {
        return {
            eventSaved:false
        }
    },

    methods: {
        getEvent() {
            this.event = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.event, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.eventSaved = true;
                    this.$router.push({ name: 'eclm.plugin.eventDetail', params: { id: this.event.id } });
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: 'Failed to create an Event entity! Ensure that all fields are filled properly!',
                    message: exception
                });
            });
        }
    }
});
