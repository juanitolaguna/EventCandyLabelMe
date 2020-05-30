import template from './eclm-candy-detail.html.twig';
import './eclm-candy-detail.scss'

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('eclm-candy-detail', {
    template: template,

    metaInfo() {
        return {
            title: 'Candy Details'
        };
    },
});
