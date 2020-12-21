import Vue from 'vue/dist/vue.js';
import App from "./app/App.vue";
import EventCandyLabelMe from "./script/event-candy-label-me.plugin"

window.PluginManager.register('EventCandyLabelMe', EventCandyLabelMe, '.main-navigation-link');

export const bus = new Vue()

if (window.location.pathname === '/label-me') {
    new Vue({
        el: '#eclm_storefront_app',
        template: '<App />',
        components: {App},
    });
}






