const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

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
    var fontPath = 'public/fonts';

    mix.sass([
      'app.scss',
      'btn.scss',
      'table.scss',
      'form.scss',
      'project.scss',
      'deployment.scss',
      'fa-extensions.scss'
    ], 'public/css/')

    .webpack('app.js')

    .styles([
      "./node_modules/jquery-colorbox/example1/colorbox.css"
    ], 'public/css/vendor.css')

    // Passing in
    .scripts([
      'vue/vue-globals.js',
    ], 'public/js/vue/vue-kit.js')

    // Fonts
    .copy('node_modules/font-awesome/fonts', fontPath)
    .copy('node_modules/bootstrap-sass/assets/fonts/bootstrap', fontPath +'/bootstrap');

    // mix.version([
    //     'public/js/vue/vue-kit.js',
    //     'public/css/app.css',
    // ], 'public/build');
});

