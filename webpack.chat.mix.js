const mix = require('laravel-mix');

require('laravel-mix-merge-manifest');

mix.mergeManifest();

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.copyDirectory('Modules/Chat/Resources/assets/images', 'public/chat/assets/images');
mix.copyDirectory('Modules/Chat/Resources/assets/fonts', 'public/chat/assets/fonts');
mix.copyDirectory('Modules/Chat/Resources/assets/icons', 'public/chat/assets/icons');

mix.copy('node_modules/video.js/dist/video-js.css', 'public/chat/assets/css/video-js.css');
mix.copy('node_modules/@coreui/coreui/dist/css/coreui.min.css', 'public/chat/assets/css/coreui.min.css');
mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css', 'public/chat/assets/css/bootstrap.min.css');
mix.copy('node_modules/simple-line-icons/css/simple-line-icons.css', 'public/chat/assets/css/simple-line-icons.css');
mix.copy('node_modules/jquery-toast-plugin/dist/jquery.toast.min.css', 'public/chat/assets/css/jquery.toast.min.css');

mix.copy('node_modules/jquery/dist/jquery.min.js', 'public/chat/assets/js/jquery.min.js');
mix.copy('node_modules/video.js/dist/video.min.js', 'public/chat/assets/js/video.min.js');
mix.copy('node_modules/popper.js/dist/umd/popper.min.js', 'public/chat/assets/js/popper.min.js');
mix.copy('node_modules/@coreui/coreui/dist/js/coreui.min.js', 'public/chat/assets/js/coreui.min.js');
mix.copy('node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js', 'public/chat/assets/js/perfect-scrollbar.min.js');
mix.copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'public/chat/assets/js/bootstrap.bundle.min.js');
mix.copy('node_modules/jquery-toast-plugin/dist/jquery.toast.min.js', 'public/chat/assets/js/jquery.toast.min.js');
mix.copy('node_modules/emojione/lib/js/emojione.min.js', 'public/chat/assets/js/emojione.min.js');
mix.copy('node_modules/sweetalert2/dist/sweetalert2.all.min.js', 'public/chat/assets/js/sweetalert2.all.min.js');
mix.copy('node_modules/icheck/', 'public/chat/assets/icheck/');
mix.copy('Modules/Chat/Resources/assets/js/moment-with-locales.js',
    'public/chat/assets/js/moment-with-locales.min.js')

mix.copy('Modules/Chat/Resources/assets/js/min', 'public/chat/assets/js');
mix.copy('Modules/Chat/Resources/assets/css', 'public/chat/assets/css');

mix.js('Modules/Chat/Resources/assets/js/app.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/chat.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/notification.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/set_user_status.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/profile.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/custom.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/auth-forms.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/set-user-on-off.js', 'public/chat/assets/js').
    js('Modules/Chat/Resources/assets/js/admin/users/user.js',
        'public/chat/assets/js/admin/users').
    js('Modules/Chat/Resources/assets/js/admin/meetings/meetings.js',
        'public/chat/assets/js/admin/meetings').
    js('Modules/Chat/Resources/assets/js/admin/meetings/meeting_index.js',
        'public/chat/assets/js/admin/meetings').
    js('Modules/Chat/Resources/assets/js/admin/meetings/member_meeting_index.js',
        'public/chat/assets/js/admin/meetings').
    js('Modules/Chat/Resources/assets/js/admin/users/edit_user.js',
        'public/chat/assets/js/admin/users').
    js('Modules/Chat/Resources/assets/js/admin/roles/role.js',
        'public/chat/assets/js/admin/roles').
    js('Modules/Chat/Resources/assets/js/admin/roles/create_edit_role.js',
        'public/chat/assets/js/admin/roles').
    js('Modules/Chat/Resources/assets/js/admin/reported_users/reported_users.js',
        'public/chat/assets/js/admin/reported_users').
    js('Modules/Chat/Resources/assets/js/admin/front_cms/front-cms.js',
        'public/chat/assets/js/admin/front_cms').
    js('Modules/Chat/Resources/assets/js/custom-datatables.js',
        'public/chat/assets/js/custom-datatables.js');

mix.sass('Modules/Chat/Resources/assets/sass/style.scss', 'public/chat/assets/css').
    sass('Modules/Chat/Resources/assets/sass/font-awesome.scss', 'public/chat/assets/css').
    sass('Modules/Chat/Resources/assets/sass/admin_panel.scss', 'public/chat/assets/css').
    sass('Modules/Chat/Resources/assets/landing-page-scss/scss/landing-page-style.scss', 'public/chat/assets/css').
    sass('Modules/Chat/Resources/assets/sass/new-conversation.scss', 'public/chat/assets/css/new-conversation.css').
    sass('Modules/Chat/Resources/assets/sass/custom-style.scss', 'public/chat/assets/css');

mix.version();
/*

mix.babel('public/assets/js/app.js', 'public/assets/js/app.js').
    babel('public/assets/js/chat.js', 'public/assets/js/chat.js').
    babel('public/assets/js/notification.js',
        'public/assets/js/notification.js')
    .babel('public/assets/js/set_user_status.js',
        'public/assets/js/set_user_status.js')
   .babel('public/assets/js/profile.js', 'public/assets/js/profile.js')
   .babel('public/assets/js/custom.js', 'public/assets/js/custom.js')
   .babel('public/assets/js/set-user-on-off.js', 'public/assets/js/set-user-on-off.js')
   .babel('public/assets/js/auth-forms.js', 'public/assets/js/auth-forms.js').version();

mix.babel('public/assets/js/jquery.min.js', 'public/assets/js/jquery.min.js')
   .babel('public/assets/js/video.min.js', 'public/assets/js/video.min.js')
   .babel('public/assets/js/popper.min.js', 'public/assets/js/popper.min.js')
   .babel('public/assets/js/coreui.min.js', 'public/assets/js/coreui.min.js')
   .babel('public/assets/js/perfect-scrollbar.min.js', 'public/assets/js/perfect-scrollbar.min.js')
   .babel('public/assets/js/bootstrap.min.js', 'public/assets/js/bootstrap.min.js')
   .babel('public/assets/js/jquery.toast.min.js', 'public/assets/js/jquery.toast.min.js')
   .babel('public/assets/js/emojione.min.js', 'public/assets/js/emojione.min.js')
   .babel('public/assets/js/sweetalert2.all.min.js', 'public/assets/js/sweetalert2.all.min.js');

mix.babel('public/assets/css/video-js.css', 'public/assets/css/video-js.css')
   .babel('public/assets/css/coreui.min.css', 'public/assets/css/coreui.min.css')
   .babel('public/assets/css/bootstrap.min.css', 'public/assets/css/bootstrap.min.css')
   .babel('public/assets/css/simple-line-icons.css', 'public/assets/css/simple-line-icons.css')
   .babel('public/assets/css/jquery.toast.min.css', 'public/assets/css/jquery.toast.min.css');
*/
