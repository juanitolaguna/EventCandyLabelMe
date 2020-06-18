import Vue from 'vue/dist/vue.js';
import {EclmPlugin} from "./eclm-plugin/eclm-plugin.plugin";
import App from "./app/App.vue";

const PluginManager = window.PluginManager;
PluginManager.register('EclmPlugin', EclmPlugin, '[eclm-plugin]');


new Vue({
    el: '#app',
    template: '<App/>',
    components: {App}
});
