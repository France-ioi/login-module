var mix = require('laravel-mix');

mix.setPublicPath('public')
    .js('resources/assets/js/app.js', 'public/js')
    .extract(
        [
            'jquery',
            'bootstrap-sass',
            'bootstrap-3-typeahead',
            'bootstrap-datepicker',
            'bootstrap-slider',
            'style-loader'
        ],
        'public/js/vendor.js'
    )
    .sass('resources/assets/sass/app.scss', 'public/css')
    .version();