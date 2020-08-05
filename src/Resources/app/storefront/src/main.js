import Vue from 'vue/dist/vue.js';
import App from "./app/App.vue";

export const bus = new Vue()

new Vue({
    el: '#eclm_storefront_app',
    template: '<App />',
    components: {App},
});





