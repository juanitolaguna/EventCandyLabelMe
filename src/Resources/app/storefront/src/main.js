import Vue from 'vue/dist/vue.min.js';
//import Vue from 'vue/dist/vue.js';
import App from "./app/App.vue";

import EventCandyLabelMe from "./script/event-candy-label-me.plugin"

window.PluginManager.register('EventCandyLabelMe', EventCandyLabelMe, '.main-navigation-link');

export const bus = new Vue()

const eclm = document.getElementById('eclm_storefront_app');

if (eclm !== null) {
    new Vue({
        el: '#eclm_storefront_app',
        template: '<App />',
        components: {App},
    });
}





