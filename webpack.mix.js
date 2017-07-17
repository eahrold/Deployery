const { mix } = require('laravel-mix');

var fontPath = 'public/fonts';

mix.js('resources/assets/js/app.js', 'public/js/')
    .sass('resources/assets/sass/app.scss', 'public/css/')

    .styles([
      "./node_modules/jquery-colorbox/example1/colorbox.css"
    ], 'public/css/vendor.css')

    // Fonts
    .copy('node_modules/font-awesome/fonts', fontPath)
    .copy('node_modules/bootstrap-sass/assets/fonts/bootstrap', fontPath +'/bootstrap')
;

if (mix.inProduction()) {
    mix.version();
}