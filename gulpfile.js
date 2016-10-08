const elixir = require('laravel-elixir');
require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    var node_module_path = "../../../node_modules";

    mix.sass([
      'app.scss',
      'btn.scss',
      'table.scss',
      'form.scss',
      'project.scss',
      'deployment.scss',
      'fa-extensions.scss'
    ], 'public/css/');

    mix.webpack('header-helper.js');
    mix.webpack('app.js');

    // Passing in
    mix.scripts([
      'vue/vue-globals.js',
      'vue/vue-resource.js',
    ], 'public/js/vue/vue-kit.js');

    // Passing in
    mix.scripts([
       // node_module_path+'/noty/js/noty/packaged/jquery.noty.packaged.min.js',
       node_module_path+'/socket.io-client/socket.io.js',
       'alerter.js',
    ], 'public/js/vendor.js')

    mix.version([
        'public/js/vue/vue-kit.js',
        'public/js/vendor.js',
        'public/css/app.css',
    ]);
});

