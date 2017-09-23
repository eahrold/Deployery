
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Register components
 */

require('./vue/Forms/FormComponentRegistry.js');

Vue.component('projects', require('./vue/components/Projects/Projects.vue'));
Vue.component('project', require('./vue/components/Projects/Project.vue'));

Vue.component('servers', require('./vue/components/Servers/Servers.vue'));
Vue.component('history', require('./vue/components/History/History.vue'));
Vue.component('history-modal', require('./vue/components/History/HistoryModal.vue'));

Vue.component('scripts', require('./vue/components/Scripts/Scripts.vue'));
Vue.component('configs', require('./vue/components/Configs/Configs.vue'));

Vue.component('deployment', require('./vue/components/Deployments/Deployment.vue'));
Vue.component('deployments', require('./vue/components/Deployments/Deployments.vue'));

Vue.component('trash-button', require('./vue/components/Partials/TrashButton.vue'));


/**
 * Register Mixins
 */
import { LocalTime } from './vue/mixins/LocalTime.js';
Vue.mixin(LocalTime);

import { Alerter } from './alerter';
Vue.prototype.$alerter = Alerter;


/**
 * Setup the router
 */
const routes = [
    {
        path: '/',
        name: 'projects.list',
        component: require('./vue/components/Projects/Projects.vue'),
    },

    {
        path: '/projects/:project_id',
        name: 'projects.edit',
        component: require('./vue/components/Projects/Project.vue'),

        children : [
            { path: 'servers/:server_id', component: require('./vue/components/Servers/Servers.vue')},
            { path: 'configs/:server_id', component: require('./vue/components/Configs/Configs.vue')},
            { path: 'scripts/:server_id', component: require('./vue/components/Scripts/Scripts.vue')},
        ]
    },
]

import VueRouter from 'vue-router';
Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: routes,
});

window.bus = new Vue({});

/**
 * Setup the App
 */
const app = new Vue({
    router,
    el: '#app'
});
