import template from './sw-admin-menu-item.html.twig';
import  './sw-admin-menu-item.scss';

const { Component } = Shopware;

Component.override('sw-admin-menu-item', {
    template,

    methods: {
        isEventCandyLabelMePath(path) {
            return path === 'eclm.plugin.index';
        }

    },
});