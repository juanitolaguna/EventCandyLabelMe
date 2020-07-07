import template from './eclm-index.html.twig';
import './eclm-index.scss';


Shopware.Component.register('eclm-index', {
    template: template,

    data() {
        return {
            baseUrl: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    created() {
        this.baseUrl = window.location.origin;
    }



});
