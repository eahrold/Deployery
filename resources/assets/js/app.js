
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Register components
 */

require('./vue2/Forms/FormComponentRegistry.js');

Vue.component('projects', require('./vue2/components/Projects/Projects.vue'));
Vue.component('project', require('./vue2/components/Projects/Project.vue'));

Vue.component('servers', require('./vue2/components/Servers/Servers.vue'));
Vue.component('history', require('./vue2/components/History/History.vue'));
Vue.component('history-modal', require('./vue2/components/History/HistoryModal.vue'));

Vue.component('scripts', require('./vue2/components/Scripts/Scripts.vue'));
Vue.component('configs', require('./vue2/components/Configs/Configs.vue'));

Vue.component('deployment', require('./vue2/components/Deployments/Deployment.vue'));
Vue.component('deployments', require('./vue2/components/Deployments/Deployments.vue'));

Vue.component('trash-button', require('./vue2/components/Partials/TrashButton.vue'));


/**
 * Register Mixins
 */
import { LocalTime } from './vue2/mixins/LocalTime.js';
Vue.mixin(LocalTime);

/**
 * Setup the router
 */
const routes = [
    {
        path: '/projects/:id',
        component: require('./vue2/components/Projects/Project.vue'),

        children : [
            { path: 'servers/:server_id', component: require('./vue2/components/Servers/Servers.vue')},
            { path: 'configs/:server_id', component: require('./vue2/components/Configs/Configs.vue')},
            { path: 'scripts/:server_id', component: require('./vue2/components/Scripts/Scripts.vue')},
        ]
    },
]


var VueRouter = require('vue-router');
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
