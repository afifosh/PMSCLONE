const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.js('resources/js/formbuilder.js', 'public/js/')
  .sass('resources/sass/app.scss', 'public/css')
  .mergeManifest();


/*
 |--------------------------------------------------------------------------
 | Browsersync Reloading
 |--------------------------------------------------------------------------
 |
 | BrowserSync can automatically monitor your files for changes, and inject your changes into the browser without requiring a manual refresh.
 | You may enable support for this by calling the mix.browserSync() method:
 | Make Sure to run `php artisan serve` and `yarn watch` command to run Browser Sync functionality
 | Refer official documentation for more information: https://laravel.com/docs/9.x/mix#browsersync-reloading
 */

mix.browserSync('http://pms.pk/');
