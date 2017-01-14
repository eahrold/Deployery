
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */
Vue.component('servers', require('./vue/components/Servers/Servers.vue'));
Vue.component('history', require('./vue/components/History/History.vue'));
Vue.component('scripts', require('./vue/components/Scripts/Scripts.vue'));
Vue.component('configs', require('./vue/components/Configs/Configs.vue'));

Vue.component('deployment', require('./vue/components/Deployments/Deployment.vue'));
Vue.component('deployments', require('./vue/components/Deployments/Deployments.vue'));

Vue.component('trash-button', require('./vue/components/partials/TrashButton.vue'));

require('./vue/components/Projects.vue');

// const app = new Vue({
//     el: 'body'
// });
