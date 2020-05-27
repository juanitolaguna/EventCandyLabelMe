import template from './eclm-index.html.twig';
import './eclm-index.scss';

Shopware.Component.register('eclm-index', {
    template: template,

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },
});
