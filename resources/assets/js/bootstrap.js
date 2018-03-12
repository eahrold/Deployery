
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

let axios = window.axios = require('axios');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

if (Deployery.apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${Deployery.apiToken}`
}

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.axios.interceptors.response.use(
    (response)=>{
        return response;
  },(error)=>{
        if(error.response.status === 401) {
            alert('Looks like your session has expired. reloading the page');
            window.location = window.location;
            return;
        }
        // Do something with response error
        return Promise.reject(error);
  }
);

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');

Object.defineProperty(Vue.prototype, '$http', {
  get () {
    return axios
  }
})

window.noty = require('noty');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo"

if (Deployery.pusherKey) {
    window.echo = new Echo({
        broadcaster: 'pusher',
        key: Deployery.pusherKey
    });
}


