var elixir = require('laravel-elixir');

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

elixir(function(mix) {

    mix.sass([
      'app.scss',
      'table.scss',
      'form.scss',
    ], 'public/css/');

    // Passing in
    mix.scripts([
       'vue/vue.js',
       'vue/vue-resource.js',
       'vue/vue-validator.js',
       //'vue/vue-strap.js',
       //'vue/vue-sortable-directive.js',
       //'vue/vue-datepicker-directive.js',
       //'vue/vue-tinymce-directive.js',
       //'vue/vue-dropzone-directive.js',
       //'vue/vue-filepicker-directive.js',
       //'vue/vue-selectize.js',
       //'vue/vue-globals.js',
    ], 'public/js/vue/vue-kit.js')

    mix.version([
        'public/js/vue/vue-kit.js',
        'public/css/app.css'
    ]);

});

