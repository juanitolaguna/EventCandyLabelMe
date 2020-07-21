import template from './sw-order-detail-base.html.twig';
import './sw-order-detail-base.scss';

const { Component } = Shopware;

Component.override('sw-order-detail-base', {
    template,

    computed: {
        details() {
            const details = [];
            this.order.lineItems.forEach((lineitem) => {
                if (lineitem.type === "event-candy-label-me") {
                    const item = {}
                    item['event'] = lineitem.payload.event.name;
                    item['label'] = lineitem.payload.label.name;
                    item['package'] = lineitem.payload.eclm_package.name;
                    item['candy'] = lineitem.payload.candy.name;
                    item['packageUrl'] = lineitem.payload.eclm_package.thumbnail.url
                    const thumbnail = lineitem.payload.label.thumbnails.filter(t => t.width === 400);
                    item['labelUrl'] = thumbnail[0].url;
                    details.push(item);
                }
            })
            if (details.length) {
                return details;
            } else {
                return false;
            }

        },


    },



});
