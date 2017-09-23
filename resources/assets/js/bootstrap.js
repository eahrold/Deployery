
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');
require('vue-resource');

window.noty = require('noty');


/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.interceptors.push((request, next) => {

    request.headers.set('Accept', 'application/json');
    request.headers.set('Content-Type', 'application/json');
    request.headers.set('X-CSRF-TOKEN', Deployery.csrfToken);

    if (Deployery.apiToken) {
        request.headers.set('Authorization', 'Bearer ' + Deployery.apiToken);
    }

    // continue to next interceptor
    next((response) => {
        if(!response.ok) {
            if(response.status == 401) {
                alert('Oops, looks like the your session has expired. reloading the page');
                window.location = window.location;
            }
        }
    });
});

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


