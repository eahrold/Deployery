
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
            { path: 'info', name: 'projects.info', component: require('./vue/components/Projects/ProjectInfo.vue')},
            { path: 'servers', name: 'projects.servers', component: require('./vue/components/Servers/Servers.vue')},
            { path: 'history', name: 'projects.history', component: require('./vue/components/History/History.vue')},
            { path: 'configs', name: 'projects.configs', component: require('./vue/components/Configs/Configs.vue')},
            { path: 'scripts', name: 'projects.scripts', component: require('./vue/components/Scripts/Scripts.vue')},
            { path: 'details', name: 'projects.details', component: require('./vue/components/Projects/ProjectDetails.vue')},
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
